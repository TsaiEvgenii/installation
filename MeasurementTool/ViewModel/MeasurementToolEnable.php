<?php
declare(strict_types=1);

namespace BelVG\MeasurementTool\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use BelVG\MeasurementTool\Block\Cart\MeasurementElements;
use Magento\Authorization\Model\UserContextInterface;
use BelVG\MeasurementTool\Model\Service\CustomerElementsManager;

class MeasurementToolEnable implements ArgumentInterface
{
    public function __construct(
        private readonly MeasurementElements $measurementElementsBlock,
        private readonly UserContextInterface $userContext,
        private readonly CustomerElementsManager $customerElementsManager
    ) {
    }

    private function getCustomerId()
    {
        $customerId = $this->userContext->getUserId();
        return $customerId;
    }

    public function isEnable(): bool
    {
        return $this->measurementElementsBlock->getMeasurementToolConfig()['is_enabled'] &&
            $this->customerElementsManager->getCustomerElements($this->getCustomerId());
    }
}
