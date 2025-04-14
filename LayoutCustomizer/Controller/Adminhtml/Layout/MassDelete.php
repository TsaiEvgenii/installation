<?php

namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

class MassDelete extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{
    public $itemCollectionFactory;
    public $dataRepository;
    public $filter;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \BelVG\LayoutCustomizer\Model\ResourceModel\Layout\CollectionFactory $itemCollectionFactory,
        \BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface $layoutPropRepo
    ) {
        $this->dataRepository = $layoutPropRepo;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->filter = $filter;

        parent::__construct($context, $coreRegistry);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->itemCollectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $itemData = $this->dataRepository->getById($item->getId());
            $this->dataRepository->delete($itemData);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        return $this->createRedirect('*/*/');
    }

    protected function createRedirect($path, array $params = [])
    {
        $storeId = $this->getRequest()->getParam('store');
        return parent::createRedirect(
            $path,
            array_merge(['store' => $storeId], $params));
    }
}
