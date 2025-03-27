<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class GetInstallationItemFromOrder
{
    public function __construct(
        protected InstallationProductConfig $installationProductConfig,
        protected PriceCurrencyInterface $priceCurrency,
    ) {
    }

    public function get(OrderInterface $order): ?OrderItemInterface
    {
        foreach ($order->getItems() as $item) {
            if ($this->isInstallation($item)) {
                return $item;
            }
        }

        return null;
    }

    public function getItemAdditionalOptionsText(OrderInterface $order): array
    {
        $options = [];

        $installationItem = $this->get($order);
        $additionalOptions = $installationItem->getProductOptionByCode('additional_options') ?? [];
        foreach ($additionalOptions as $additionalOption) {
            if (isset($additionalOption['hidden']) && $additionalOption['hidden'] === true) {
                continue;
            }
            $label = $additionalOption['label'] ?? '';
            $price = $additionalOption['price'] ?? 0;
            $formattedPrice = $this->priceCurrency->format(
                $price,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $order->getStore()
            );
            $options[] = $label . ': ' . $formattedPrice;
        }
        $options[] = __('Total: ') . $this->priceCurrency->format(
                $installationItem->getRowTotalInclTax(),
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $order->getStore()
            );

        return $options;
    }

    public function isInstallation(OrderItemInterface $item): bool
    {
        return $item->getSku()
            && $item->getSku() === $this->installationProductConfig->getProductSku();
    }
}