<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\OrderUpgrader\Model\Service\Quote;


use BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterfaceFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Framework\App\State as AppState;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\QuoteFactory;

class PriceMap
{
    private const LOG_PREFIX = '[BelVG_OrderUpgrader::PriceMapService]: ';

    public function __construct(
        private readonly PriceMapEntityInterfaceFactory $priceMapEntityFactory,
        private readonly AppState $appState,
        private readonly QuoteFactory $quoteFactory,
        private readonly AddressFactory $addressFactory,
        private readonly ProductFactory $productFactory,
        private readonly ProductResourceModel $productResourceModel,
    ) {
    }

    /**
     * Generates a price map for different material and option combinations
     *
     * @param \Magento\Quote\Model\Quote $currentQuote Current quote
     * @param array $dataStructure Materials data structure
     * @param \BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface[] $materials Materials list
     * @param array $options Options list
     * @return array Price map with calculated differences
     */
    public function getMap($currentQuote, $dataStructure, $materials, $options): array
    {
        $priceMap = [];
        $shippingInclTax = (float)$currentQuote->getShippingAddress()->getData('shipping_incl_tax') ?: 0;

        foreach ($options as $optionCode => $optionData) {
            $optionValues = $optionData['values'];
            foreach ($optionValues as $optionValueData) {
                $optionValue = $optionValueData['value'] ?? '';
                $optionAlternativeQuote = $this->getAlternativeQuote(
                    $currentQuote,
                    $dataStructure,
                    null,
                    $optionCode,
                    $optionValue
                );

                /** @var \BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface $priceMaterialMapEntity */
                $priceMaterialMapEntity = $this->priceMapEntityFactory->create();
                $priceMaterialMapEntity->setId($optionCode . '-' . $optionValue);

                // Calculate price difference with shipping and round to avoid floating point errors
                $priceDifference = round(
                    ((float)$optionAlternativeQuote->getGrandTotal() + $shippingInclTax)
                    - (float)$currentQuote->getGrandTotal(),
                    4 // 4 decimal places should be enough for currency precision
                );

                // Zero out very small differences that are due to floating point arithmetic errors
                if (abs($priceDifference) < 0.0001) {
                    $priceDifference = 0;
                }

                $priceMaterialMapEntity->setPrice($priceDifference);
                $priceMap[] = $priceMaterialMapEntity;
                unset($optionAlternativeQuote);
            }
        }

        /** @var \BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface $material */
        foreach ($materials as $material) {
            $materialId = $material->getId();
            $materialAlternativeQuote = $this->getAlternativeQuote(
                $currentQuote,
                $dataStructure,
                $materialId
            );

            /** @var \BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface $priceMaterialMapEntity */
            $priceMaterialMapEntity = $this->priceMapEntityFactory->create();
            $priceMaterialMapEntity->setId('material_' . $materialId);

            // Calculate price difference with shipping and round to avoid floating point errors
            $priceDifference = round(
                ((float)$materialAlternativeQuote->getGrandTotal() + $shippingInclTax)
                - (float)$currentQuote->getGrandTotal(),
                4 // 4 decimal places should be enough for currency precision
            );

            // Zero out very small differences that are due to floating point arithmetic errors
            if (abs($priceDifference) < 0.0001) {
                $priceDifference = 0;
            }

            $priceMaterialMapEntity->setPrice($priceDifference);
            $priceMap[] = $priceMaterialMapEntity;
            unset($materialAlternativeQuote);

            foreach ($options as $optionCode => $optionData) {
                $optionValues = $optionData['values'];
                foreach ($optionValues as $optionValueData) {
                    $optionValue = $optionValueData['value'] ?? '';
                    $materialOptionAlternativeQuote = $this->getAlternativeQuote(
                        $currentQuote,
                        $dataStructure,
                        $materialId,
                        $optionCode,
                        $optionValue
                    );

                    /** @var \BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface $priceMaterialMapEntity */
                    $priceMaterialMapEntity = $this->priceMapEntityFactory->create();
                    $priceMaterialMapEntity->setId('material_' . $materialId . ":" . $optionCode . '-' . $optionValue);

                    // Calculate price difference with shipping and round to avoid floating point errors
                    $priceDifference = round(
                        ((float)$materialOptionAlternativeQuote->getGrandTotal() + $shippingInclTax)
                        - (float)$currentQuote->getGrandTotal(),
                        4 // 4 decimal places should be enough for currency precision
                    );

                    // Zero out very small differences that are due to floating point arithmetic errors
                    if (abs($priceDifference) < 0.0001) {
                        $priceDifference = 0;
                    }

                    $priceMaterialMapEntity->setPrice($priceDifference);
                    $priceMap[] = $priceMaterialMapEntity;
                    unset($materialOptionAlternativeQuote);
                }
            }
        }

        return $priceMap;
    }

    /**
     * Creates an alternative quote based on the current quote with specific changes
     *
     * @param \Magento\Quote\Model\Quote $currentQuote Current quote
     * @param array $dataStructure Materials data structure
     * @param string|null $materialId Material ID to use (null = use current)
     * @param string|null $optionCode Option code to change
     * @param string|null $optionValueCode Option value to set
     * @return \Magento\Quote\Model\Quote Alternative quote with changes
     */
    protected function getAlternativeQuote(
        $currentQuote,
        $dataStructure,
        $materialId = null,
        $optionCode = null,
        $optionValueCode = null
    ) {
        return $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_FRONTEND,
            function () use ($currentQuote, $dataStructure, $materialId, $optionCode, $optionValueCode) {
                $alternativeQuoteItems = [];
                $storeId = $currentQuote->getStoreId();
                $alternativeQuote = $this->quoteFactory->create();

                // Copy essential quote data
                $alternativeQuote->setStoreId($storeId);
                $alternativeQuote->setCustomerGroupId($currentQuote->getCustomerGroupId());
                $alternativeQuote->setCustomerId($currentQuote->getCustomerId());
                $alternativeQuote->setCustomerEmail($currentQuote->getCustomerEmail());
                $alternativeQuote->setCustomerIsGuest($currentQuote->getCustomerIsGuest());
                $alternativeQuote->setCustomerTaxClassId($currentQuote->getCustomerTaxClassId());

                // Copy currency data
                $alternativeQuote->setCurrency($currentQuote->getCurrency());

                // Create addresses
                $alternativeShippingAddress = $this->addressFactory->create();
                $alternativeBillingAddress = $this->addressFactory->create();

                // Copy address data from original quote if available
                if ($currentQuote->getShippingAddress()) {
                    $shippingAddress = $currentQuote->getShippingAddress();
                    $alternativeShippingAddress->setCustomerId($shippingAddress->getCustomerId());
                    $alternativeShippingAddress->setCountryId($shippingAddress->getCountryId());
                    $alternativeShippingAddress->setRegionId($shippingAddress->getRegionId());
                    $alternativeShippingAddress->setStreet($shippingAddress->getStreet());
                    $alternativeShippingAddress->setCity($shippingAddress->getCity());
                    $alternativeShippingAddress->setPostcode($shippingAddress->getPostcode());
                    $alternativeShippingAddress->setTelephone($shippingAddress->getTelephone());

                    // Copy shipping method if exists
                    if ($shippingAddress->getShippingMethod()) {
                        $alternativeShippingAddress->setShippingMethod($shippingAddress->getShippingMethod());
                        $alternativeShippingAddress->setShippingDescription($shippingAddress->getShippingDescription());
                    }
                }

                if ($currentQuote->getBillingAddress()) {
                    $billingAddress = $currentQuote->getBillingAddress();
                    $alternativeBillingAddress->setCustomerId($billingAddress->getCustomerId());
                    $alternativeBillingAddress->setCountryId($billingAddress->getCountryId());
                    $alternativeBillingAddress->setRegionId($billingAddress->getRegionId());
                    $alternativeBillingAddress->setStreet($billingAddress->getStreet());
                    $alternativeBillingAddress->setCity($billingAddress->getCity());
                    $alternativeBillingAddress->setPostcode($billingAddress->getPostcode());
                    $alternativeBillingAddress->setTelephone($billingAddress->getTelephone());
                }

                // Process each item in the data structure
                foreach ($dataStructure as $entityData) {
                    $altProduct = $this->productFactory->create();
                    $altProduct->setData('store_id', $storeId);
                    $request = new DataObject();

                    // Find correct material structure based on requested material or current
                    $currentMaterialStructure = [];
                    foreach ($entityData['materials'] as $entityMaterialData) {
                        if ($materialId === null) {
                            if ($entityMaterialData['current']) {
                                $currentMaterialStructure = $entityMaterialData;
                                break;
                            }
                        }
                        if ((int)$entityMaterialData['material_id'] === (int)$materialId) {
                            $currentMaterialStructure = $entityMaterialData;
                            break;
                        }
                        if (empty($currentMaterialStructure) && $entityMaterialData['current']) {
                            $currentMaterialStructure = $entityMaterialData;
                        }
                    }

                    // Determine which options to use based on requested changes
                    if (
                        $optionCode !== null
                        && $optionValueCode !== null
                        && ($currentMaterialStructure['options_structure'][$optionCode][$optionValueCode]['map'] ?? false)
                    ) {
                        $options = $currentMaterialStructure['options_structure'][$optionCode][$optionValueCode]['map'];
                    } else {
                        $options = $currentMaterialStructure['options_map'];
                    }

                    // Create product request
                    $productId = $currentMaterialStructure['product_id'];
                    $request->setData('product', $productId);
                    $request->setData('qty', $entityData['qty']);
                    $request->setData('options', $options);

                    // Load product and add to quote
                    $this->productResourceModel->load($altProduct, $productId);
                    $item = $alternativeQuote->addProduct($altProduct, $request);

                    if (is_string($item)) {
                        throw new \RuntimeException(self::LOG_PREFIX . '[apply] ' . $item);
                    }

                    // Add item to addresses
                    $alternativeShippingAddress->addItem($item);
                    $alternativeBillingAddress->addItem($item);
                    $alternativeQuoteItems[] = $item;

                    // Free memory
                    unset($item);
                    unset($request);
                    unset($altProduct);
                }

                // Set items and addresses to quote
                $alternativeQuote->setItems($alternativeQuoteItems);
                $alternativeQuote->setShippingAddress($alternativeShippingAddress);
                $alternativeQuote->setBillingAddress($alternativeBillingAddress);

                // Apply the same coupon code if any
                if ($couponCode = $currentQuote->getCouponCode()) {
                    $alternativeQuote->setCouponCode($couponCode);
                }

                // Collect totals including discounts and taxes
                $alternativeQuote->collectTotals();

                return $alternativeQuote;
            }
        );
    }
}