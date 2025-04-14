<?php


namespace BelVG\LayoutCustomizer\Controller\Adminhtml\LayoutStore;

class Edit extends \BelVG\LayoutCustomizer\Controller\Adminhtml\LayoutStore
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('layoutstore_id');
        $model = $this->_objectManager->create(\BelVG\LayoutCustomizer\Model\LayoutStore::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Layoutstore no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('belvg_layoutcustomizer_layoutstore', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Layoutstore') : __('New Layoutstore'),
            $id ? __('Edit Layoutstore') : __('New Layoutstore')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Layoutstores'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Layoutstore %1', $model->getId()) : __('New Layoutstore'));
        return $resultPage;
    }
}