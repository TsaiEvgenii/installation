<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use BelVG\MeasurementRequest\Model\Config\MeasurementProductConfig;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class GetInstallationItemFromOrder
{
    public function __construct(
        protected InstallationProductConfig $installationProductConfig,
        protected PriceCurrencyInterface $priceCurrency,
        protected MeasurementProductConfig $measurementProductConfig
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
        $measurementAmount = $this->getMeasurementAmount($order);
        if ($measurementAmount !== null) {
            $options[] = __('Kontrolopmåling :') . $this->priceCurrency->format(
                    $measurementAmount,
                    false,
                    PriceCurrencyInterface::DEFAULT_PRECISION,
                    $order->getStore()
                );
        }

        $options[] = __('Total: ') . $this->priceCurrency->format(
                $installationItem->getRowTotalInclTax() + $measurementAmount,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $order->getStore()
            );

        return $options;
    }
    protected function getMeasurementAmount(OrderInterface $order): ?float
    {
        foreach ($order->getItems() as $orderItem){
            if($orderItem->getProductType() === $this->measurementProductConfig->getProductType()){
                return (float)$orderItem->getRowTotalInclTax();
            }
        }
        return 0;
    }

    public function isInstallation(OrderItemInterface $item): bool
    {
        return $item->getSku()
            && $item->getSku() === $this->installationProductConfig->getProductSku();
    }
}