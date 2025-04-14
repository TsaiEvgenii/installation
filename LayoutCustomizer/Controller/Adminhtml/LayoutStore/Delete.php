<?php


namespace BelVG\LayoutCustomizer\Controller\Adminhtml\LayoutStore;

class Delete extends \BelVG\LayoutCustomizer\Controller\Adminhtml\LayoutStore
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('layoutstore_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\BelVG\LayoutCustomizer\Model\LayoutStore::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Layoutstore.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['layoutstore_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Layoutstore to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}