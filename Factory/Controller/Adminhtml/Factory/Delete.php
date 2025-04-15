<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory;

use Magento\Framework\Exception;

class Delete extends \BelVG\Factory\Controller\Adminhtml\Factory
{
    public function execute()
    {
        $this->factoryHelper->initStore($this->getRequest());

        try {
            // Load
            $object = $this->factoryHelper->initObject($this->getRequest(), true);

            // Delete
            $this->factoryHelper->deleteObject($object);

            // Add success message
            $this->messageManager->addSuccessMessage(__('Factory has been deleted'));

        } catch (Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Failed to load Factory'));
        }

        // Redirect to list
        return $this->createRedirect('*/*/');
    }
}
