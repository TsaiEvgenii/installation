<?php


namespace BelVG\LayoutCustomizer\Model;

use BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore as ResourceLayoutStore;
use BelVG\LayoutCustomizer\Api\Data\LayoutStoreSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use BelVG\LayoutCustomizer\Api\LayoutStoreRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore\CollectionFactory as LayoutStoreCollectionFactory;

class LayoutStoreRepository implements LayoutStoreRepositoryInterface
{

    protected $resource;

    protected $layoutStoreCollectionFactory;

    protected $searchResultsFactory;

    protected $dataLayoutStoreFactory;

    private $storeManager;

    protected $extensionAttributesJoinProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $dataObjectProcessor;

    protected $extensibleDataObjectConverter;
    protected $layoutStoreFactory;


    /**
     * @param ResourceLayoutStore $resource
     * @param LayoutStoreFactory $layoutStoreFactory
     * @param LayoutStoreInterfaceFactory $dataLayoutStoreFactory
     * @param LayoutStoreCollectionFactory $layoutStoreCollectionFactory
     * @param LayoutStoreSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceLayoutStore $resource,
        LayoutStoreFactory $layoutStoreFactory,
        LayoutStoreInterfaceFactory $dataLayoutStoreFactory,
        LayoutStoreCollectionFactory $layoutStoreCollectionFactory,
        LayoutStoreSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->layoutStoreFactory = $layoutStoreFactory;
        $this->layoutStoreCollectionFactory = $layoutStoreCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataLayoutStoreFactory = $dataLayoutStoreFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface $layoutStore
    ) {
        /* if (empty($layoutStore->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $layoutStore->setStoreId($storeId);
        } */

        $layoutStoreData = $this->extensibleDataObjectConverter->toNestedArray(
            $layoutStore,
            [],
            \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface::class
        );

        $layoutStoreModel = $this->layoutStoreFactory->create()->setData($layoutStoreData);

        try {
            $this->resource->save($layoutStoreModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the layoutStore: %1',
                $exception->getMessage()
            ));
        }
        return $layoutStoreModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($layoutStoreId)
    {
        $layoutStore = $this->layoutStoreFactory->create();
        $this->resource->load($layoutStore, $layoutStoreId);
        if (!$layoutStore->getId()) {
            throw new NoSuchEntityException(__('LayoutStore with id "%1" does not exist.', $layoutStoreId));
        }
        return $layoutStore->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->layoutStoreCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface $layoutStore
    ) {
        try {
            $layoutStoreModel = $this->layoutStoreFactory->create();
            $this->resource->load($layoutStoreModel, $layoutStore->getLayoutstoreId());
            $this->resource->delete($layoutStoreModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the LayoutStore: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($layoutStoreId)
    {
        return $this->delete($this->getById($layoutStoreId));
    }
}