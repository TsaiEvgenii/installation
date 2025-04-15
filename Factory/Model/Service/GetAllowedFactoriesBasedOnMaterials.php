<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Service;

use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\Store;
use BelVG\Factory\Model\Data\FactoryWithMaterial;
use BelVG\Factory\Api\Data\FactoryWithMaterialInterfaceFactory;
use BelVG\Factory\Api\Data\FactoryWithMaterialInterface;

class GetAllowedFactoriesBasedOnMaterials
{
    private FactoryWithMaterialInterfaceFactory $factoryWithMaterialDTOFactory;
    private ResourceConnection $resourceConnection;

    public function __construct(
        FactoryWithMaterialInterfaceFactory $factoryWithMaterialDTOFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->factoryWithMaterialDTOFactory = $factoryWithMaterialDTOFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param int|null $storeId
     * @param bool $loadDefault
     * @return FactoryWithMaterialInterface[]
     */
    public function getFactoriesMaterials(
        ?int $storeId = null,
        bool $loadDefault = true
    ) :iterable {
        $connection = $this->resourceConnection->getConnection();

        $storeIds = array_merge([$storeId], ($loadDefault ? [Store::DEFAULT_STORE_ID] : []));
        // If values('is_active' in particular) exist for specific store in factory_store table then first and foremost join them,
        // otherwise join values for default store
        $chooseStoreSubSelect = $connection->quoteInto(
            "(SELECT MAX(store_id) FROM belvg_factory_store WHERE factory_id = factory_material.factory_id AND store_id IN (?))",
            $storeIds
        );

        $select = $connection->select()
            ->distinct()
            ->from(
                ['factory_material' => $connection->getTableName('belvg_factory_material')],
                ['*']
            )->join(
                ['layout_material' => $connection->getTableName('belvg_layoutmaterial_layoutmaterial')],
                // FACTORY MATERIAL REQUIRED STORE DATA OR DEFAULT
                $connection->quoteInto(
                    'factory_material.material_id = layout_material.layoutmaterial_id AND factory_material.store_id IN (?)',
                    $storeIds
                ),
                ['material_identifier' => 'layout_material.identifier'])
            ->join(
                ['factory_store' => $connection->getTableName('belvg_factory_store')],
                // FACTORY REQUIRED STORE DATA OR DEFAULT
                $connection->quoteInto(
                    'factory_material.factory_id = factory_store.factory_id AND factory_store.store_id IN (?)',
                    new \Zend_Db_Expr($chooseStoreSubSelect)
                ),
                []
            )
            ->where('factory_store.is_active = (?)', 1)
            ->order(['factory_material.priority DESC']);

        $result = [];
        foreach ($connection->fetchAll($select) as $item) {
            $result[] = $this->factoryWithMaterialDTOFactory->create(
                [
                    'data' => [
                        'factory_id' => $item['factory_id'],
                        'material_id' => $item['material_id'],
                        'material_identifier' => $item['material_identifier'],
                        'store_id' => $item['store_id'],
                        'priority' => $item['priority'],
                    ],
                ]
            );
        }

        $this->addMaterialsWithProducers($result);

        return $this->mergeFactories($result);
    }

    /**
     * https://youtrack.belvgdev.com/issue/SD-1974 [If material do not assigned with factory, it's items are missed from sub-orders]
     *
     * @param $result
     */
    private function addMaterialsWithProducers(&$result) :void {
        $defaultFactory = null; //@todo: should be some logic upon the default factory, instead of choose the first one (but in general, every material should have Factory)
        $materials = [];

        /** @var FactoryWithMaterial $resultItem */
        foreach ($result as $resultItem) {
            $materials[$resultItem->getMaterialIdentifier()] = $resultItem->getMaterialIdentifier();
            if ($defaultFactory === null) {
                $defaultFactory = $resultItem->getFactoryId();
            }
        }
        unset($resultItem);

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['layoutmaterial' => $connection->getTableName('belvg_layoutmaterial_layoutmaterial')],
                ['*']
            )
            ->where('layoutmaterial.identifier NOT IN (?)', $materials);

        foreach ($connection->fetchAll($select) as $item) {
            $result[] = $this->factoryWithMaterialDTOFactory->create(
                [
                    'data' => [
                        'factory_id' => $defaultFactory,
                        'material_id' => $item['layoutmaterial_id'],
                        'material_identifier' => $item['identifier'],
                        'store_id' => 0,
                        'priority' => 0,
                    ],
                ]
            );
        }
    }

    /**
     * https://youtrack.belvgdev.com/issue/SD-1807
     * We shouldn't split order in case when priority for different factories is the same and this material
     * can be produced on the same factory. We need to select automatically one of them
     *
     * @param array $sortedFactories
     * @return array
     */
    private function mergeFactories(array $sortedFactories) :array {
        $result = [];

        //Fill array with materials can be produced by factory
        $factoriesMergedByMaterials = $this->mergeFactoriesByMaterials($sortedFactories);

        foreach ($factoriesMergedByMaterials as $factoryId => $factoryMaterials) {
            foreach ($factoryMaterials as $factoryMaterial) {
                $materialProducers = [];
                /** @var FactoryWithMaterial $factory */
                foreach($sortedFactories as $factory) {
                    if ($factory->getMaterialIdentifier() === $factoryMaterial &&
                        $factory->getFactoryId() == $factoryId
                    ) {
                        $materialProducerCandidate = [
                            'material' => $factory->getMaterialIdentifier(),
                            'factoryId' => $factory->getFactoryId(),
                            'priority' => $factory->getPriority()
                        ];
                        if ($this->isProducerDeterminedForMaterial($materialProducerCandidate, $result) === false) {
                            $materialProducers[] = $materialProducerCandidate;
                        }
                    }
                }
                unset($factory);
                unset($materialProducerCandidate);

                if ($materialProducers) {
                    //Sorting by priority
                    uasort($materialProducers, [$this, 'sortFactoriesByPriorityCallback']);

                    foreach ($materialProducers as $materialProducer) {
                        //Check, probably this material is already producing by some factory
                        if ($this->isProducerDeterminedForMaterial($materialProducer, $result)) {
                            break; //this material is already exists (can be produced by other factory)
                        }

                        //Factories with materials match
                        $preferableFactories = $this->getPreferableFactories($result);

                        $resultCandidate = $this->getResultCandidate(
                            $materialProducer['factoryId'],
                            $materialProducer['material'],
                            $sortedFactories
                        );
                        if ($preferableFactories) {
                            foreach ($preferableFactories as $preferableFactoryId) {
                                foreach ($materialProducers as $materialProducerCandidate) {
                                    if ($materialProducerCandidate['factoryId'] == $preferableFactoryId) {
                                        $resultCandidate = $this->getResultCandidate(
                                            $materialProducerCandidate['factoryId'],
                                            $materialProducerCandidate['material'],
                                            $sortedFactories
                                        );
                                        break 2;
                                    }
                                }
                                unset($materialProducerCandidate);
                            }
                            unset($preferableFactoryId);
                        }

                        $this->addFactoryToResult($resultCandidate, $result);
                    }
                    unset($materialProducer);
                }
            }
            unset($factoryMaterial);
        }
        unset($factoryId);
        unset($factoryMaterials);

        return $result;
    }

    /**
     * Fill array with materials can be produced by factory
     *
     * @param array $sortedFactories
     * @return array
     */
    private function mergeFactoriesByMaterials(array $sortedFactories) :array {
        $factoriesMergedByMaterials = [];
        foreach ($sortedFactories as $factory) {
            if (!isset($factoriesMergedByMaterials[$factory['factory_id']])) {
                $factoriesMergedByMaterials[$factory['factory_id']] = [];
            }

            $factoriesMergedByMaterials[$factory['factory_id']][] = $factory['material_identifier'];
        }
        unset($factory);

        return $factoriesMergedByMaterials;
    }

    /**
     * @param array $factoryA
     * @param array $factoryB
     * @return int
     */
    private function sortFactoriesByPriorityCallback(
        array $factoryA,
        array $factoryB
    ) :int {
        if ($factoryA['priority'] == $factoryB['priority']) {
            return 0;
        }

        return ($factoryA['priority'] > $factoryB['priority']) ? -1 : 1;
    }

    /**
     * @param array $materialProducer
     * @param array $producers
     * @return bool
     */
    private function isProducerDeterminedForMaterial(
        array $materialProducer,
        array $producers
    ) :bool {
        /** @var FactoryWithMaterial $producer */
        foreach ($producers as $producer) {
            if ($producer->getMaterialIdentifier() == $materialProducer['material']) {
                return $producer->getPriority() > $materialProducer['priority'];
            }
        }
        unset($producer);

        return false;
    }

    /**
     * @param FactoryWithMaterial $resultCandidate
     * @param array $result
     */
    private function addFactoryToResult(
        FactoryWithMaterial $resultCandidate,
        array &$result
    ) :void {
        $removalResult = $this->removeMaterial(
            $resultCandidate->getMaterialIdentifier(),
            $result,
            $resultCandidate->getPriority()
        );

        if ($removalResult) {
            $result[] = $resultCandidate;
        }
    }

    /**
     * @param string $material
     * @param array $producers
     * @param int|null $priority
     * @return bool
     */
    private function removeMaterial(
        string $material,
        array &$producers,
        int $priority = null
    ) :bool {
        $wasRemoved = false;
        $sameProducerExists = false;

        foreach ($producers as $key => $producer) {
            /** @var FactoryWithMaterial $producer */
            if ($material == $producer->getMaterialIdentifier()) {
                $sameProducerExists = true;

                if ($priority && $priority <= $producer->getPriority()) {
                    break;
                }

                unset($producers[$key]);
                $wasRemoved = true;
            }
        }

        return ($wasRemoved || $sameProducerExists === false);
    }

    /**
     * @param array $result
     * @return array
     */
    private function getPreferableFactories(array $result) :array {
        $preferableFactories = [];
        if ($result) {
            /** @var FactoryWithMaterial $resultFactory */
            foreach ($result as $resultFactory) {
                $preferableFactories[$resultFactory->getFactoryId()] = $resultFactory->getFactoryId();
            }
            unset($resultFactory);
        }

        return $preferableFactories;
    }

    /**
     * @param int $factoryId
     * @param string $material
     * @param array $producers
     * @return FactoryWithMaterial|null
     */
    private function getResultCandidate(
        int $factoryId,
        string $material,
        array $producers
    ) :?FactoryWithMaterial {
        foreach ($producers as $factory) {
            if (
                $factory->getFactoryId() == $factoryId &&
                $factory->getMaterialIdentifier() == $material
            ) {
                return $factory;
            }
        }

        return null;
    }
}
