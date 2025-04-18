<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\OrderUpgrader\Model\Service\Quote;


use BelVG\LayoutMaterial\Api\Service\FamilyInterface;
use BelVG\MageWorxOptionBase\Model\Service\GetAlternativeOptions;
use BelVG\MageWorxOptionBase\Model\Service\ProductOptions;
use BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterfaceFactory;
use BelVG\OrderUpgrader\Api\Data\OptionsToUpgradeInterface;
use BelVG\OrderUpgrader\Api\Data\OptionsToUpgradeInterfaceFactory;
use BelVG\OrderUpgrader\Api\Webapi\GetOptionsToUpgradeForQuoteInterface;
use BelVG\OrderUpgrader\Model\Service\Config;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResourceModel;
use Magento\Quote\Model\Quote;

class GetOptionsToUpgradeForQuoteService implements GetOptionsToUpgradeForQuoteInterface
{
    public function __construct(
        protected GetQuoteService $getQuoteService,
        protected FamilyInterface $familyService,
        protected OptionsToUpgradeInterfaceFactory $optionsToUpgradeFactory,
        protected QuoteIdMaskFactory $quoteIdMaskFactory,
        protected QuoteIdMaskResourceModel $quoteIdMaskResource,
        protected ProductOptions $productOptionsService,
        protected MaterialUpgradeInterfaceFactory $materialUpgradeFactory,
        protected GetAlternativeOptions $getAlternativeOptionsService,
        protected PriceMap $priceMapService,
        protected Config $config
    ){}

    /**
     * Get options for logged-in customer
     *
     * @param int $cartId
     * @return OptionsToUpgradeInterface
     */
    public function getOptions($cartId): OptionsToUpgradeInterface
    {
        $quoteObj = $this->getQuoteService->getQuote((int)$cartId);
        return $this->processQuoteOptions($quoteObj);
    }

    /**
     * Get options for guest customer
     *
     * @param string $cartId
     * @return OptionsToUpgradeInterface
     */
    public function getOptionsForGuest($cartId): OptionsToUpgradeInterface
    {
        // For guest carts, we need to unmask the quote ID first
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $this->quoteIdMaskResource->load($quoteIdMask, $cartId, 'masked_id');
        $quoteObj = $this->getQuoteService->getQuote((int)$quoteIdMask->getData('quote_id'));

        return $this->processQuoteOptions($quoteObj);
    }

    /**
     * Common method for processing quote options
     *
     * @param Quote $quoteObj
     * @return OptionsToUpgradeInterface
     */
    private function processQuoteOptions(Quote $quoteObj): OptionsToUpgradeInterface
    {
        /** @var OptionsToUpgradeInterface $optionsToUpgrade */
        $optionsToUpgrade = $this->optionsToUpgradeFactory->create();

        // Process materials data structure
        $materialsDataStructure = $this->getMaterialsDataStructure($quoteObj);
        $materialsMap = $this->getMaterialsMap($materialsDataStructure);
        $optionsToUpgrade->setMaterialsMap($materialsMap);

        // Get options to change and price map
        $optionsToChange = $this->config->getUpgradeOptions($quoteObj->getStoreId());
        $priceMap = $this->priceMapService->getMap(
            $quoteObj,
            $materialsDataStructure,
            $materialsMap,
            $optionsToChange
        );
        $optionsToUpgrade->setPriceMap($priceMap);
        $optionsToUpgrade->setOptions($optionsToChange);

        return $optionsToUpgrade;
    }

    protected function getMaterialsMap($materialsDataStructure): array
    {
        $materialsMap = [];
        $materials = [];
        foreach ($materialsDataStructure as $structureElement) {
            foreach ($structureElement['materials'] as $materialData) {
                $materialId = $materialData['material_id'];
                if ($materials[$materialId] ?? false) {
                    $materials[$materialId]['skus'][] = $structureElement['product_sku'];
                } else {
                    $materials[$materialId] = [
                        'label' => $materialData['material_name'],
                        'image' => $materialData['material_image'] ?? '',
                        'skus'  => [$structureElement['product_sku']]
                    ];
                }
            }
        }

        $allSkus = [];
        foreach ($materials as $material) {
            $allSkus = array_merge($allSkus, $material['skus']);
        }
        $uniqueSkus = array_unique($allSkus);
        foreach ($materials as &$item) {
            $missingSkus = array_values(array_diff($uniqueSkus, $item['skus']));
            if (!empty($missingSkus)) {
                $item['missing_skus'] = $missingSkus;
            }
        }
        unset($item);

        foreach ($materials as $materialId => $material){
            /** @var \BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface $materialEntity */
            $materialEntity = $this->materialUpgradeFactory->create();
            $materialEntity->setId((string)$materialId);
            $materialEntity->setLabel($material['label'] ?? '');
            $materialEntity->setImage($material['image'] ?? null);
            $materialEntity->setMissingSku(
                isset($material['missing_skus']) && is_array($material['missing_skus'])
                    ? implode(',', $material['missing_skus'])
                    : null
            );
            $materialsMap[] = $materialEntity;
        }

        return $materialsMap;
    }

    public function getMaterialsDataStructure($quote): array
    {
        $structure = [];
        $storeId = $quote->getStoreId();
        $quoteItems = $quote->getAllVisibleItems();
        $materialImages = $this->config->getMaterialImages($storeId);

        foreach ($quoteItems as $quoteItem) {
            $product = $quoteItem->getProduct();
            $structure[$quoteItem->getId()] =[
                'product_id' => $product->getId(),
                'product_sku' => $product->getSku(),
                'qty'=> $quoteItem->getQty()
            ];
            $family = $this->familyService->getFamilyForProduct($product);
            /** @var \BelVG\LayoutCustomizer\Model\Data\Layout $familyRelative */
            foreach ($family as $familyRelative) {
                $identifier = $familyRelative->getIdentifier();
                //Todo: need to understand how it should work with DK products
                if (str_starts_with($identifier, 'DK-')) {
                    continue;
                }
                $relativeProduct = $familyRelative->getExtensionAttributes()->getProduct();
                $material = $familyRelative->getExtensionAttributes()->getMaterial();

                $materialId = $material->getId();

                $optionsStructure = [];
                $relatedProductData = $this->getRelatedProductData(
                    (int)$relativeProduct->getId(),
                    (int)$quoteItem->getItemId(),
                    (int)$storeId
                );
                $optionsValuesMapToAddToQuote = $relatedProductData['options'] ?? [];
                $productOptionsData = $relatedProductData['allOptionsData'];

                $optionsToChange = $this->config->getUpgradeOptions($storeId);
                foreach ($optionsToChange as $optionKey => $changeOptionData) {
                    $optionValues = array_column($changeOptionData['values'], 'value');
                    $optionData = $this->getOptionByKey($productOptionsData, $optionKey);
                    if(isset($optionData['values'])) {
                        foreach ($optionData['values'] as $optionValue) {
                            $valueCode = $optionValue['value_code'];
                            if (!in_array($valueCode, $optionValues)) {
                                continue;
                            }
                            $optionId = $optionData['option_id'];
                            $valueId = $optionValue['value_id'];
                            $currentValue = false;
                            if ($optionsValuesMapToAddToQuote[$optionId] == $valueId) {
                                $currentValue = true;
                            }

                            $mapForOption = $optionsValuesMapToAddToQuote;
                            $mapForOption[$optionData['option_id']] = $optionValue['value_id'];
                            $optionsStructure[$optionKey][$valueCode] = [
                                'map' => $mapForOption,
                                'current_value' => $currentValue
                            ];
                        }
                    }
                }

                $materialImage = isset($materialImages[$materialId]) ? $materialImages[$materialId]['image_url'] : null;

                $structure[$quoteItem->getId()]['materials'][] = [
                    'product_id' => $relativeProduct->getId(),
                    'product_sku' => $relativeProduct->getSku(),
                    'material_id' => $material->getId(),
                    'material_name' => $material->getName(),
                    'material_image' => $materialImage,
                    'options_map'=> $optionsValuesMapToAddToQuote,
                    'options_matched'=> $relatedProductData['allOptionsMatched'] ?? [],
                    'current' => $product->getSku() === $relativeProduct->getSku(),
                    'options_structure' => $optionsStructure
                ];
            }
        }

        return $structure;
    }

    function getOptionByKey(array $options, string $searchKey): array
    {
        return array_values(array_filter($options, fn($option) => $option['option_key'] === $searchKey))[0] ?? [];
    }

    protected function getRelatedProductData($alternativeProductId, $quoteItemId, $storeId): array
    {
        $options = [];
        $altOptions = $this->getAlternativeOptionsService->getAlternativeOptions(
            $alternativeProductId,
            $quoteItemId,
            $storeId
        );

        foreach ($altOptions as $altOption) {
            $options[$altOption['option_id']] = $altOption['option_type_id'];
        }
        unset($altOptions);
        unset($altOption);
        $allEnabledOptionsWithValues = $this->productOptionsService->getEnabledOptionsWithValuesByProductId(
            $alternativeProductId,
            $storeId
        );


        $allOptionsMatched = true;
        foreach ($allEnabledOptionsWithValues as $optionId => $optionData) {
            if ($options[$optionId] ?? false) {
                continue;
            }
            $defaultValueData = [];
            $optionValues = $optionData['values'] ?? [];
            foreach ($optionValues as $optionValue) {
                if ($optionValue['default'] == 0) {
                    continue;
                }
                $defaultValueData = $optionValue;
            }
            if ($defaultValueData) {
                $options[$optionId] = $defaultValueData['value_id'];
                $allOptionsMatched = false;
            } elseif (count($optionValues) > 0) {
                $options[$optionId] = $optionValues[0]['value_id'];
                $allOptionsMatched = false;
            }
        }

        $disabledOptions =  $this->productOptionsService->getDisabledOptionsWithValuesByProductId(
            $alternativeProductId,
            $storeId
        );
        //Remove disabled options
        foreach ($options as $optionKey => $optionValue) {
            if (array_key_exists($optionKey, $disabledOptions)) {
                unset($options[$optionKey]);
                continue;
            }
            //Substitute disabled options values
            if ($allEnabledOptionsWithValues[$optionKey] ?? false) {
                $matched = false;
                $enabledValues = $allEnabledOptionsWithValues[$optionKey]['values'];
                foreach ($enabledValues as $enabledValue) {
                    if ((int)$optionValue === (int)$enabledValue['value_id']) {
                        $matched = true;
                    }

                }
                if ($matched === false) {
                    $valueToSubstitute = $enabledValues[0]['value_id'];
                    $options[$optionKey] = $valueToSubstitute;
                }
            }
        }

        return [
            'options' => $options,
            'allOptionsData' => $allEnabledOptionsWithValues,
            'allOptionsMatched' => $allOptionsMatched
        ];
    }
}