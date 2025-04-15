<?php
namespace BelVG\Factory\Model;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\Data\FactoryMaterialInterface;
use BelVG\Factory\Api\Data\FactoryMaterialSearchResultsInterfaceFactory;
use BelVG\Factory\Api\FactoryMaterialRepositoryInterface;
use BelVG\Factory\Model\FactoryMaterialFactory as ModelFactory;
use BelVG\Factory\Model\ResourceModel\FactoryMaterial as Resource;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\StoreManagerInterface;

class FactoryMaterialRepository implements FactoryMaterialRepositoryInterface
{
    use DefaultStoreId;

    protected $resource;
    protected $collectionFactory;
    protected $modelFactory;
    protected $attributeJoinProcessor;
    protected $searchCollectionProcessor;
    protected $searchResultsFactory;
    protected $extensibleDataObjectConverter;
    protected $filterBuilder;
    protected $filterGroupBuilder;
    protected $searchCriteriaBuilder;

    // [factory_id => searchRes]
    protected $factoryMaterialListCache = [];
    //
    protected $factoryMaterialCache = [];

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceConnection;

    /**
     * @param Resource $resource
     * @param Resource\CollectionFactory $collectionFactory
     * @param FactoryMaterialFactory $modelFactory
     * @param JoinProcessorInterface $attributeJoinProcessor
     * @param CollectionProcessorInterface $searchCollectionProcessor
     * @param FactoryMaterialSearchResultsInterfaceFactory $searchResultsFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Resource $resource,
        Resource\CollectionFactory $collectionFactory,
        ModelFactory $modelFactory,
        JoinProcessorInterface $attributeJoinProcessor,
        CollectionProcessorInterface $searchCollectionProcessor,
        FactoryMaterialSearchResultsInterfaceFactory $searchResultsFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
        $this->attributeJoinProcessor = $attributeJoinProcessor;
        $this->searchCollectionProcessor = $searchCollectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
    }

    public function getById($factoryMaterialId)
    {
        $factoryMaterial = $this->modelFactory->create();
        $this->resource->load($factoryMaterial, $factoryMaterialId);
        if (!$factoryMaterial->getId())
            throw new Exception\NoSuchEntityException(
                __('Factory material not found by ID "%1"', $factoryMaterialId));
        return $factoryMaterial->getDataModel();
    }

    public function getByFactoryAndMaterialId(FactoryInterface $factory, $materialId)
    {
        $cache = &$this->factoryMaterialCache;
        $factoryId = $factory->getFactoryId();
        if (!isset($cache[$factoryId][$materialId])) {
            $cache[$factoryId][$materialId] =
                $this->_getByFactoryAndMaterialId($factory, $materialId);
        }
        return $cache[$factoryId][$materialId];
    }

    public function getList(SearchCriteriaInterface $criteria)
    {
        // Load items
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($this->getStoreId());
        $this->attributeJoinProcessor->process($collection, FactoryMaterialInterface::class);
        $this->searchCollectionProcessor->process($criteria, $collection);
        $items = $collection->walk('getDataModel');

        // Return search results
        return $this->searchResultsFactory
            ->create()
            ->setSearchCriteria($criteria)
            ->setItems($items)
            ->setTotalCount($collection->getSize());
    }

    public function getListByFactory(FactoryInterface $factory, bool $checkActivity = true)
    {
        $cache = &$this->factoryMaterialListCache;
        $factoryId = $factory->getFactoryId();
        if (!isset($cache[$factoryId])) {
            $cache[$factoryId] = $this->_getListByFactory($factory, $checkActivity);
        }
        return $cache[$factoryId];
    }

    public function save(FactoryMaterialInterface $factoryMaterial)
    {
        // Get data
        $data = $this->extensibleDataObjectConverter
            ->toNestedArray($factoryMaterial, [], FactoryMaterialInterface::class);
        // Create model
        $model = $this->modelFactory
            ->create();
        // Load
        $data = $this->loadIfExist($model, $data);
        $this->checkIsExistSpecialForStore($data);
        // Update data
        $model->addData($data);
        try {
            $this->resource->save($model);
        } catch (\Exception $e) {
            throw new Exception\CouldNotSaveException(
                __('Could not save a factory material: %1', $e->getMessage()));
        }
        return $model->getDataModel();
    }

    /**
     * @param FactoryMaterial $model
     * @param string[] $data
     * @return string[]
     */
    protected function loadIfExist(FactoryMaterial $model, array $data): array
    {
        if ($data[FactoryMaterialInterface::FACTORY_MATERIAL_ID]) {
            $model->load((int) $data[FactoryMaterialInterface::FACTORY_MATERIAL_ID]);
            $modelStoreId = (int) $model->getDataByKey(FactoryMaterialInterface::STORE_ID);
            if ($modelStoreId === 0 && (int) $data[FactoryMaterialInterface::STORE_ID] !== 0) {
                $data[FactoryMaterialInterface::STORE_ID] = 0;
            }
        }
        return $data;
    }

    /**
     * Checks whether material exists specially for store(s)
     *
     * @param string[] $data
     * @throws Exception\NoSuchEntityException
     * @throws CouldNotSaveException
     */
    protected function checkIsExistSpecialForStore(array $data): void
    {
        if (!$data[FactoryMaterialInterface::FACTORY_MATERIAL_ID] &&
            (int) $data[FactoryMaterialInterface::STORE_ID] === 0) {
            $factoryMaterials = $this->getListByFactoryAndMaterialIdWithNonDefaultStore(
                (int) $data[FactoryMaterialInterface::FACTORY_ID],
                (int) $data[FactoryMaterialInterface::MATERIAL_ID]
            );
            if (is_array($factoryMaterials) && count($factoryMaterials) > 0) {
                $storeNames = [];
                foreach ($factoryMaterials as $factoryMaterial) {
                    $storeNames[] = $this->storeManager
                        ->getStore($factoryMaterial[FactoryMaterialInterface::STORE_ID])->getName();
                }
                throw new CouldNotSaveException(
                    __(
                        'Could not save a material %1 for all stores, please delete same material on %2',
                        $data[FactoryMaterialInterface::MATERIAL_ID],
                        implode(",", $storeNames),
                    )
                );
            }
        }
    }

    public function delete(FactoryMaterialInterface $factoryMaterial)
    {
        $factoryMaterialModel = $this->modelFactory->create();
        try {
            $this->resource->load($factoryMaterialModel, $factoryMaterial->getFactoryMaterialId());
            $this->resource->delete($factoryMaterialModel);
        } catch (\Exception $e) {
            throw new Exception\CouldNotDeleteException(
                __('Could not delete factory: %1', $e->getMessage()));
        }
        return true;
    }

    public function deleteById($factoryMaterialId)
    {
        return $this->delete($this->getById($factoryMaterialId));
    }

    public function _getByFactoryAndMaterialId(FactoryInterface $factory, $materialId,  bool $checkActivity = true)
    {
        $list = $this->_getListByFactory($factory, $checkActivity);
        foreach ($list->getItems() as $factoryMaterial) {
            if ((int) $factoryMaterial->getMaterialId() === (int) $materialId) {
                return $factoryMaterial;
            }
        }
        return null;
    }

    protected function _getListByFactory(FactoryInterface $factory, bool $checkActivity = true)
    {
        $factoryId = $factory->getFactoryId();
        if ($checkActivity && !$factory->getIsActive()) {
            $factoryId = null;
        }
        $filterFactoryId = $this->filterBuilder
            ->setField(FactoryInterface::FACTORY_ID)
            ->setValue($factoryId)
            ->setConditionType('eq')
            ->create();
        $filterGroupFactoryId = $this->filterGroupBuilder->addFilter($filterFactoryId)->create();
        $criteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroupFactoryId])->create();
        return $this->getList($criteria);
    }

    /**
     * @param int $factoryId
     * @param int $materialId
     * @return array|bool
     */
    protected function getListByFactoryAndMaterialIdWithNonDefaultStore(int $factoryId, int $materialId)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = $connection->select()
            ->from($connection->getTableName('belvg_factory_material'))
            ->where(FactoryMaterialInterface::FACTORY_ID . ' = ?', $factoryId)
            ->where(FactoryMaterialInterface::MATERIAL_ID . ' = ?', $materialId)
            ->where(FactoryMaterialInterface::STORE_ID . ' != ?', 0);
        return $connection->fetchAll($sql);
    }
}
