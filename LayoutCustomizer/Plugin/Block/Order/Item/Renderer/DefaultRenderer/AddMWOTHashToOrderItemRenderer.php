<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\LayoutCustomizer\Plugin\Block\Order\Item\Renderer\DefaultRenderer;


use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use Psr\Log\LoggerInterface;

class AddMWOTHashToOrderItemRenderer
{
    private const LOG_PREFIX = '[BelVG_LayoutCustomizer::AddMWOTHashToOrderItemRendererPlugin]: ';

    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function afterGetItemOptions(
        DefaultRenderer $subject,
        $result
    ) {
        try {
            $item = $subject->getOrderItem();
            $product = $item->getProduct();
            if ($product) {
                foreach ($result as &$option) {
                    $optionId = $option['option_id'] ?? false;
                    $optionValue = $option['option_value'] ?? false;
                    if (!$optionId || !$optionValue) {
                        continue;
                    }
                    $productOption = $product->getOptionByid($optionId);
                    if (
                        $productOption
                        && $productOptionValue = $productOption->getValueById($optionValue)
                    ) {
                        $option['mageworx_optiontemplates_group_option_type_id']
                            = $productOptionValue->getData('mageworx_optiontemplates_group_option_type_id');
                    }
                }
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