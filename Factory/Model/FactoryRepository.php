<?php
namespace BelVG\Factory\Model;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\Data\FactorySearchResultsInterfaceFactory;
use BelVG\Factory\Api\FactoryRepositoryInterface;
use BelVG\Factory\Model\FactoryFactory as ModelFactory;
use BelVG\Factory\Model\ResourceModel\Factory as Resource;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception;

class FactoryRepository implements FactoryRepositoryInterface
{
    protected $resource;
    protected $collectionFactory;
    protected $modelFactory;
    protected $attributeJoinProcessor;
    protected $searchCollectionProcessor;
    protected $searchResultsFactory;
    protected $extensibleDataObjectConverter;

    public function __construct(
        Resource $resource,
        Resource\CollectionFactory $collectionFactory,
        ModelFactory $modelFactory,
        JoinProcessorInterface $attributeJoinProcessor,
        CollectionProcessorInterface $searchCollectionProcessor,
        FactorySearchResultsInterfaceFactory $searchResultsFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter)
    {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
        $this->attributeJoinProcessor = $attributeJoinProcessor;
        $this->searchCollectionProcessor = $searchCollectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    public function getById($factoryId, $storeId = null)
    {
        $factory = $this->modelFactory
            ->create()
            ->setStoreId($storeId);
        $this->resource->load($factory, $factoryId);
        if (!$factory->getId())
            throw new Exception\NoSuchEntityException(
                __('Factory not found by ID "%1"', $factoryId));

        return $factory->getDataModel();
    }

    public function getList(SearchCriteriaInterface $criteria)
    {
        // Load items
        $collection = $this->collectionFactory->create();
        $this->attributeJoinProcessor->process($collection, FactoryInterface::class);
        $this->searchCollectionProcessor->process($criteria, $collection);
        $items = $collection->walk('getDataModel');

        // Return search results
        return $this->searchResultsFactory
            ->create()
            ->setSearchCriteria($criteria)
            ->setItems($items)
            ->setTotalCount($collection->getSize());

    }

    public function save(FactoryInterface $factory, $storeId = null)
    {
        // Get data
        $factoryData = $this->extensibleDataObjectConverter
            ->toNestedArray($factory, [], FactoryInterface::class);
        // Create model
        $factoryModel = $this->modelFactory
            ->create()
            ->setData($factoryData)
            ->setStoreId($storeId);
        try {
            // Save
            $this->resource->save($factoryModel);
        } catch (\Exception $e) {
            throw new Exception\CouldNotSaveException(
                __('Could not save a factory: %1', $e->getMessage()));
        }
        return $factoryModel->getDataModel();
    }

    public function delete(FactoryInterface $factory)
    {
        $factoryModel = $this->modelFactory->create();
        try {
            $this->resource->load($factoryModel, $factory->getFactoryId());
            $this->resource->delete($factoryModel);
        } catch (\Exception $e) {
            throw new Exception\CouldNotDeleteException(
                __('Could not delete factory: %1', $e->getMessage()));
        }
        return true;
    }

    public function deleteById($factoryId)
    {
        return $this->delete($this->getById($factoryId));
    }
}
