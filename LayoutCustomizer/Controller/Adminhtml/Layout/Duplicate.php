<?php


namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

class Duplicate extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{
    public $duplicateLayoutService;
    public $layoutRepo;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \BelVG\LayoutCustomizer\Api\Service\DuplicateLayoutDataInterface $duplicateLayoutService,
        \BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface $layoutRepo
    ) {
        $this->duplicateLayoutService = $duplicateLayoutService;
        $this->layoutRepo = $layoutRepo;

        parent::__construct($context, $coreRegistry);
    }

    /**
     * Duplicate action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // check if we know what should be duplicated
        $id = $this->getRequest()->getParam('layout_id');
        if ($id) {
            try {
                // init model and duplicate
                $model = $this->layoutRepo->getById($id, 0);

                //duplicate
                $newModel = $this->duplicateLayoutService->copyAndSave($model);

                // display success message
                $this->messageManager->addSuccessMessage(__('You duplicated the Layout, new ID is %1.', $newModel->getLayoutId()));
                // go to grid
                return $this->createRedirect('*/*/');

            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $this->createRedirect('*/*/edit', ['layout_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Layout to duplicate.'));
        // go to grid
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
