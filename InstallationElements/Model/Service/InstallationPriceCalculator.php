<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;

use BelVG\InstallationElements\Api\Data\AdditionalPriceInterface;
use BelVG\InstallationElements\Api\Data\InstallationInterface;
use BelVG\LayoutCustomizer\Model\Helper\QuoteItemOptionManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Item;
use BelVG\InstallationElements\Model\Service\GetQuoteQty;

class InstallationPriceCalculator
{
    public function __construct(
        protected QuoteItemOptionManagement $quoteItemOptionManagement,
        protected Config $installationElementsConfig,
        protected GetQuoteQty $getQuoteQtyService,
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    /**
     * Calculate installation price based on cart and installation data
     *
     * @param string|CartInterface $cart
     * @param InstallationInterface $installationData
     * @return array
     * @throws NoSuchEntityException
     */
    public function calculate(string|CartInterface $cart, InstallationInterface $installationData): array
    {
        $price = 0;
        $basePrice = 0;
        $constructionWasteDisposalPriceIncluded = false;
        $constructionWasteDisposalPrice = 0;
        $internalFinishPriceIncluded = false;
        $internalFinishPrice = 0;
        $highGroundFloorPriceForOneItem = 0;
        $firstFloorPriceForOneItem = 0;
        $highGroundFloorPrice = 0;
        $firstFloorPrice = 0;
        $drivingPrice = 0;
        $additionalPrices = $installationData->getAdditionalPrices() ?? [];

        // Initialize scaffolding prices
        $scaffoldingPrice = 0;
        $scaffoldingHandlingPrice = 0;
        $scaffoldingData = $this->installationElementsConfig->getScaffoldingPriceData();

        if ($cart instanceof CartInterface) {
            // Calculate base price
            $basePrice = $this->calculateBasePrice($cart);
            $price += $basePrice;

            // Calculate construction service
            $constructionWasteDisposalPrice = $this->calculateConstructionWasteDisposalPrice($cart);
            if ($installationData->getDisposalOfConstructionWaste()) {
                $constructionWasteDisposalPriceIncluded = true;
                $price += $constructionWasteDisposalPrice;
            }

            // Calculate internal finish service
            $internalFinishPrice = $this->calculateInternalFinishPrice($cart);
            if ($installationData->getInternalFinish()) {
                $internalFinishPriceIncluded = true;
                $price += $internalFinishPrice;
            }

            // Calculate floor price and scaffolding
            $supplementPriceData = $this->installationElementsConfig->getSupplementPriceData();
            $highGroundFloorPriceForOneItem = $supplementPriceData['high_ground_floor'] ?? 0;
            $firstFloorPriceForOneItem = $supplementPriceData['assembly_1_floor'] ?? 0;

            if (
                $installationData->getInstallationHighGroundFloorQty()
                || $installationData->getInstallationFirstFloorQty()
            ) {
                if (($highGroundFloorQty = $installationData->getInstallationHighGroundFloorQty()) > 0) {
                    // Add high ground floor standard price
                    $highGroundFloorPrice = $highGroundFloorPriceForOneItem * $highGroundFloorQty;
                    $price += $highGroundFloorPrice;

                    // Calculate scaffolding for high ground floor
                    $scaffoldingPrice = (float)$scaffoldingData['high_ground_floor_start_price'];
                    $scaffoldingHandlingPrice = (float)$scaffoldingData['per_element_price'] * $highGroundFloorQty;

                    // Add scaffolding prices to total
                    $price += $scaffoldingPrice + $scaffoldingHandlingPrice;
                }

                if (($firstFloorQty = $installationData->getInstallationFirstFloorQty()) > 0) {
                    $firstFloorPrice = $firstFloorPriceForOneItem * $firstFloorQty;
                    $price += $firstFloorPrice;
                }
            }

            // Calculate driving price
            $drivingPrice = $supplementPriceData['driving'] ?? 0;
            $price += $drivingPrice;

            // Calculate additional prices
            if (count($additionalPrices) > 0) {
                /** @var AdditionalPriceInterface $additionalPrice */
                foreach ($additionalPrices as $additionalPrice) {
                    $price += $additionalPrice->getPrice();
                }
            }
        }

        // Define the flag for showing first floor note
        $showFirstFloorNote = $installationData->getInstallationFirstFloorQty() > 0;

        return [
            'price'                                => $price,
            'base_price'                           => $basePrice,
            'construction_price_included'          => $constructionWasteDisposalPriceIncluded,
            'construction_price'                   => $constructionWasteDisposalPrice,
            'internal_finish_price'                => $internalFinishPrice,
            'internal_finish_price_included'       => $internalFinishPriceIncluded,
            'internal_finish_type'                 => $installationData->getInternalFinishType(),
            'living_room_qty'                      => $installationData->getInstallationLivingRoomQty(),
            'high_ground_floor_qty'                => $installationData->getInstallationHighGroundFloorQty(),
            'high_ground_floor_price'              => $highGroundFloorPrice,
            'high_ground_floor_price_for_one_item' => $highGroundFloorPriceForOneItem,
            'first_floor_qty'                      => $installationData->getInstallationFirstFloorQty(),
            'first_floor_price'                    => $firstFloorPrice,
            'first_floor_price_for_one_item'       => $firstFloorPriceForOneItem,
            'driving_price'                        => $drivingPrice,
            'additional_prices'                    => $additionalPrices,
            'conditions_approved'                  => $installationData->getConditionsApproved(),
            'scaffolding_price'                    => $scaffoldingPrice,
            'scaffolding_handling_price'           => $scaffoldingHandlingPrice,
            'scaffolding_high_ground_floor_start_price' => (float)$scaffoldingData['high_ground_floor_start_price'],
            'scaffolding_per_element_price'        => (float)$scaffoldingData['per_element_price'],
            'show_first_floor_note'                => $showFirstFloorNote
        ];
    }
    /**
     * @throws NoSuchEntityException
     */
    public function calculateBasePrice(CartInterface $cart): float|int
    {
        $price = 0;
        $standardSettings = $this->installationElementsConfig->prepareSettingsData(
            $this->installationElementsConfig->getStandardPriceData()
        );
        $windowAndDoorPricesBySqr = [
            ...($standardSettings['window_sqr'] ?? []),
            ...($standardSettings['door_sqr'] ?? [])
        ];
        foreach ($cart->getAllVisibleItems() as $item) {
            $price += $this->calculate3LayerGlassPrice($item);
            $qty = $item->getQty();
            $sku = $item->getSku();
            foreach ($windowAndDoorPricesBySqr as $windowAndDoorPriceItem) {
                $fromSqr = floatval($windowAndDoorPriceItem['from_sqr']);
                $toSqr = floatval($windowAndDoorPriceItem['to_sqr']);
                $skuPrefixes = $windowAndDoorPriceItem['sku_prefix'] ?? [];
                if (
                    $this->isSkuMatched($sku, $skuPrefixes)
                    && $this->isSqrValueInRange($item, $fromSqr, $toSqr)
                ) {
                    $priceForItem = floatval($windowAndDoorPriceItem['price']);
                    $price += ($priceForItem * $qty);
                    break;
                }
            }
        }

        return $price;
    }

    protected function calculate3LayerGlassPrice(Item $item): int|float
    {
        $price = 0;
        $layerGlassPrice = $this->installationElementsConfig->getStandardPriceData()['layer_glass'] ?? 0;
        if ($buyRequestOption = $item->getOptionByCode('info_buyRequest')) {
            $buyRequestOptionValue = json_decode($buyRequestOption->getValue(), true);
            $factoryOptions = $buyRequestOptionValue['factory_options'] ?? [];
            foreach ($factoryOptions as $factoryOption) {
                if ($factoryOption['label'] === 'Energy class') {
                    $energyClassValue = $factoryOption['value'] ?? '';
                    if (str_contains($energyClassValue, '3 layer')) {
                        $price += ($layerGlassPrice * $item->getQty());
                    }
                }
            }
        }

        return $price;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function calculateConstructionWasteDisposalPrice(CartInterface $cart): float|int
    {
        $price = 0;
        $itemQty = $this->getQuoteQtyService->get($cart);
        $optionSettings = $this->installationElementsConfig->prepareSettingsData(
            $this->installationElementsConfig->getOptionalPriceData()
        );
        foreach ($optionSettings['construction'] as $constructionData) {
            $from = (int)$constructionData['from_items'];
            $to = (int)$constructionData['to_items'];
            if ($itemQty >= $from && $itemQty <= $to) {
                $price = floatval($constructionData['price']);
                break;
            }
        }

        return $price;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function calculateInternalFinishPrice(CartInterface $cart): float|int
    {
        $price = 0;
        $optionSettings = $this->installationElementsConfig->prepareSettingsData(
            $this->installationElementsConfig->getOptionalPriceData()
        );

        foreach ($cart->getAllVisibleItems() as $item) {
            $qty = $item->getQty();
            $sku = $item->getSku();
            foreach ($optionSettings['internal'] as $internalDataItem) {
                $skuPrefixes = $internalDataItem['sku_prefix'] ?? [];
                if ($this->isSkuMatched($sku, $skuPrefixes)) {
                    $priceForItem = floatval($internalDataItem['price']);
                    $price += ($priceForItem * $qty);
                    break;
                }
            }
        }

        return $price;
    }
    public function calculateGloorHeightPrice(){

    }

    protected function isSkuMatched(string $sku, array $skuPrefixes): bool
    {
        foreach ($skuPrefixes as $skuPrefix) {
            if (str_starts_with($sku, $skuPrefix)) {
                return true;
            }
        }

        return false;
    }

    protected function isSqrValueInRange(Item $item, float $from, float $to): bool
    {
        $dimensions = $this->quoteItemOptionManagement->getDimensions($item);
        $width = ($dimensions['width'] ?? 0) / 100;
        $height = ($dimensions['height'] ?? 0) / 100;
        $sqr = $width * $height;

        return $sqr >= $from && $sqr < $to;
    }
}