<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory;

use Magento\Framework\Exception;
use BelVG\Factory\Api\Data\FactoryInterface;

class Save extends \BelVG\Factory\Controller\Adminhtml\Factory
{
    public function execute()
    {
        $this->factoryHelper->initStore($this->getRequest());

        // Get data
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $this->createRedirect('*/*/');
        }
        if (!empty($data[FactoryInterface::IS_ACTIVE]) && $data[FactoryInterface::IS_ACTIVE] === 'false') {
            $data[FactoryInterface::IS_ACTIVE] = false;
        }

        // [field1 => '1', field2 => '0', ...]
        $useDefault = (array) $this->getRequest()->getParam('use_default', []);
        foreach ($useDefault as $field => $default) {
            if ($default) {
                $data[$field] = null;
            }
        }

        // Store form data
        $this->factoryHelper->storeFormData($data);

        try {
            // Load/create
            $factory = $this->factoryHelper->initObject($this->getRequest());

            if (!empty($data)) {
                // Save
                $factory = $this->factoryHelper->saveObject($factory, $data);

                // Add success message
                $this->messageManager->addSuccessMessage(__('Factory has been saved'));

                // Clear form data
                $this->factoryHelper->clearFormData();

                // Redirect
                return $this->getRequest()->getParam('back')
                    ? $this->createRedirect('*/*/edit', [
                        'factory_id' => $factory->getFactoryId()])
                    : $this->createRedirect('*/*/');

            } else {
                // Redirect to list
                return $this->createRedirect('*/*/');
            }

        } catch (Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Failed to save Factory'));
        }

        // Redirect back
        return $this->createRedirect('*/*/edit', ['factory_id' => $factory->getFactoryId()]);
    }
}
