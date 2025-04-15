<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Plugin\FactoryRepositoryMaterial\AfterSave\ActionsPool;

use BelVG\Factory\Api\Data\FactoryMaterialInterface;
use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\FactoryRepositoryMaterial\AfterSave\ActionInterface;
use BelVG\Factory\Api\FactoryMaterialRepositoryInterface;
use BelVG\Factory\Model\ResourceModel\FactoryMaterial as FactoryMaterialResource;

class SaveMaterials implements ActionInterface
{
    private FactoryMaterialRepositoryInterface $factoryMaterialRepo;
    private FactoryMaterialResource $factoryMaterialResource;

    public function __construct(
        FactoryMaterialRepositoryInterface $factoryMaterialRepo,
        FactoryMaterialResource $factoryMaterialResource
    ) {
        $this->factoryMaterialRepo = $factoryMaterialRepo;
        $this->factoryMaterialResource = $factoryMaterialResource;
    }

    public function execute(FactoryInterface $factory, int $storeId = 0)
    {
        $extensionAttributes = $factory->getExtensionAttributes();
        $materials = $extensionAttributes->getMaterials();

        if (is_array($materials)) {
            $connection = $this->factoryMaterialResource->getConnection();

            // Save
            $savedIds = [];
            /** @var FactoryMaterialInterface $material */
            foreach ($materials as $material) {
                $material->setStoreId((int)$storeId);
                $material->setFactoryId($factory->getFactoryId());

                $material = $this->factoryMaterialRepo->save($material);
                $savedIds[] = $material->getFactoryMaterialId();
            }

            $conditions['factory_id = ?'] = (int)$factory->getFactoryId();
            $conditions['store_id = ?'] = (int)$storeId;
            if (!empty($savedIds))
                $conditions['factory_material_id NOT IN(?)'] = $savedIds;
            $connection
                ->delete(
                    $this->factoryMaterialResource->getMainTable(),
                    $conditions);
        }
    }
}
