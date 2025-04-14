<?php


namespace BelVG\LayoutCustomizer\Model;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use BelVG\LayoutCustomizer\Api\Data\LayoutInterfaceFactory;
use BelVG\LayoutCustomizer\Api\Data\LayoutSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\CollectionFactory as LayoutCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout as ResourceLayout;

class LayoutRepository implements LayoutRepositoryInterface
{

    protected $layoutFactory;

    protected $resource;

    protected $searchResultsFactory;

    private $storeManager;

    protected $extensionAttributesJoinProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $dataObjectProcessor;

    protected $dataLayoutFactory;

    protected $extensibleDataObjectConverter;
    protected $layoutCollectionFactory;

    /**
     * @var LayoutInterface[]
     */
    protected array $layouts = [];

    /**
     * @param ResourceLayout $resource
     * @param LayoutFactory $layoutFactory
     * @param LayoutInterfaceFactory $dataLayoutFactory
     * @param LayoutCollectionFactory $layoutCollectionFactory
     * @param LayoutSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceLayout $resource,
        LayoutFactory $layoutFactory,
        LayoutInterfaceFactory $dataLayoutFactory,
        LayoutCollectionFactory $layoutCollectionFactory,
        LayoutSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->layoutFactory = $layoutFactory;
        $this->layoutCollectionFactory = $layoutCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataLayoutFactory = $dataLayoutFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * @param $layout
     * @return array
     */
    public function getLayoutData($layout): array
    {
        return $this->extensibleDataObjectConverter->toNestedArray($layout, [], LayoutInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        LayoutInterface $layout,
        $storeId = null
    ) {
        /* if (empty($layout->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $layout->setStoreId($storeId);
        } */

        $layoutData = $this->getLayoutData($layout);

        $layoutModel = $this->layoutFactory->create()->setData($layoutData);
        if (!is_null($storeId)) {
            $layoutModel->setStoreId($storeId);
        }

        try {
            $this->resource->save($layoutModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the layout: %1',
                $exception->getMessage()
            ));
        }
        return $layoutModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($layoutId, $storeId = null, $withOptions = false)
    {
        $layoutKey = $layoutId . '_' . (!is_null($storeId) ? $storeId : '');
        $layout = !empty($this->layouts[$layoutKey]) ? $this->layouts[$layoutKey] : $this->layoutFactory->create();

        if (!$layout->getId()) {

            if (!is_null($storeId)) {
                $layout->setStoreId($storeId);
            }
            $this->resource->load($layout, $layoutId);
        }

        if (!$layout->getId()) {
            unset($this->layouts[$layoutKey]);
            throw new NoSuchEntityException(__('Layout with id "%1" does not exist.', $layoutId));
        }
        $this->layouts[$layoutKey] = $layout;

        return $layout->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdentifier($identifier, $storeId = null)
    {
        $layout = $this->layoutFactory->create();
        if (!is_null($storeId)) {
            $layout->setStoreId($storeId);
        }

        $this->resource->load($layout, $identifier, LayoutInterface::IDENTIFIER);

        if (!$layout->getId()) {
            throw new NoSuchEntityException(__('Layout with identified="%1" does not exist.', $identifier));
        }
        return $layout->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->layoutCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            LayoutInterface::class
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
        LayoutInterface $layout
    ) {
        try {
            $layoutModel = $this->layoutFactory->create();
            $this->resource->load($layoutModel, $layout->getLayoutId());
            $this->resource->delete($layoutModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Layout: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($layoutId)
    {
        return $this->delete($this->getById($layoutId));
    }
}
