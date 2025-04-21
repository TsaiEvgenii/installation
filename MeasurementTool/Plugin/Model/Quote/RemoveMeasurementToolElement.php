<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Plugin\Model\Quote;


use BelVG\MeasurementTool\Api\CustomerElementRepositoryInterface;
use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;

class RemoveMeasurementToolElement
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::RemoveMeasurementToolElementPlugin]: ';

    public function __construct(
        protected CustomerElementRepositoryInterface $customerElementRepository,
        protected Session $customerSession,
        protected LoggerInterface $logger
    ) {

    }

    public function afterAddProduct(
        \Magento\Quote\Model\Quote $subject,
        \Magento\Quote\Model\Quote\Item|string $result,
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ): string|\Magento\Quote\Model\Quote\Item {
        try {

            if (is_string($result)) {
                return $result;
            }
            if (!$this->customerSession->isLoggedIn()) {
                return $result;
            }
            if (!$measurementToolElementId = $request->getData('measurement_tool_element')) {
                return $result;
            }
            $customItemName = $request->getData('belvg_custom_item_name');
            $measurementToolElement = $this->customerElementRepository->getById((int)$measurementToolElementId);
            $name = $measurementToolElement->getRoomName() . '/' . $measurementToolElement->getName();
            if ($customItemName === $name) {
                $this->customerElementRepository->delete($measurementToolElement);
            }

        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return $result;
    }
}