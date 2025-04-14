<?php


namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

class Delete extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('layout_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\BelVG\LayoutCustomizer\Model\Layout::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Layout.'));
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
        $this->messageManager->addErrorMessage(__('We can\'t find a Layout to delete.'));
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
