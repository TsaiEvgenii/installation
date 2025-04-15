<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory;

use Magento\Framework\Exception;

class Edit extends \BelVG\Factory\Controller\Adminhtml\Factory
{
    public function execute()
    {
        $this->factoryHelper->initStore($this->getRequest());

        try {
            // Load/create model
            $factory = $this->factoryHelper->initObject($this->getRequest());

            // Create page
            $title = $factory->getFactoryId()
                ? __('Edit Factory "%1" ID:%2', $factory->getName(), $factory->getFactoryId())
                : __('New Factory');
            $breadcrumb = $factory->getFactoryId() ? __('Edit Layout') : __('New Layout');
            return $this->pageHelper->createPage(
                [__('Factories'), $title],
                [$breadcrumb]);

        } catch (Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Failed to load Factory'));
        }

        return $this->createRedirect('*/*/');
    }
}
