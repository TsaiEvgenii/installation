<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Model\Service;

use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use Magento\Sales\Api\Data\OrderItemInterface;

class OrderItemService
{
    private LayoutCustomizerHelper $layoutCustomizerHelper;

    public function __construct(
        LayoutCustomizerHelper $layoutCustomizerHelper
    ) {
        $this->layoutCustomizerHelper = $layoutCustomizerHelper;
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return float
     */
    public function getWidth(OrderItemInterface $orderItem) :float {
        $overallWidth = $this->layoutCustomizerHelper->getOverallWidthOption();

        $result = $this->lookupOption($orderItem, $overallWidth);
        if (!$result) {
            //get data from M1
            $infoByRequest = $orderItem->getData('product_options')['info_buyRequest'];
            if (isset($infoByRequest['additional_options'])) {
                $result = $infoByRequest['additional_options']['width'];
            }
        }

        return (float)$result;
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return float
     */
    public function getHeight(OrderItemInterface $orderItem) :float {
        $overallHeight = $this->layoutCustomizerHelper->getOverallHeightOption();

        $result = $this->lookupOption($orderItem, $overallHeight);
        if (!$result) {
            //get data from M1
            $infoByRequest = $orderItem->getData('product_options')['info_buyRequest'];
            if (isset($infoByRequest['additional_options'])) {
                $result = $infoByRequest['additional_options']['height'];
            }
        }

        return (float)$result;
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getSize(OrderItemInterface $orderItem) :string {
        return sprintf(
            '%s x %s',
            $this->getWidth($orderItem),
            $this->getHeight($orderItem),
        );
    }

    private function lookupOption(
        OrderItemInterface $orderItem,
        string $lookupOptionId
    ) {
        $options = $orderItem->getProductOptions();

        if (isset($options['options'])) {
            foreach ($options['options'] as $option) {
                if ($this->layoutCustomizerHelper->matchDbOptionWithConfig($option['option_id'], $lookupOptionId)) {
                    return $option['value'];
                }
            }
            unset($option);
        }

        return null;
    }
}
