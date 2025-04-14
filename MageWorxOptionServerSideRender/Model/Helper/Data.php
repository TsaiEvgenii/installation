<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Helper;

use BelVG\InsideOutsideColorPrice\API\OptionPriceCalculator;
use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use BelVG\LayoutCustomizer\Model\PriceCurrency as LayoutPriceCurrency;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout as LayoutResourceModel;
use BelVG\LayoutCustomizer\Model\Service\TaxRateByStoreService;
use BelVG\LayoutOptionPriceType\Plugin\Magento\Catalog\Model\Config\Source\Product\Options\PricePlugin as BelVGLayoutOptionPriceTypePricePlugin;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedOptionProcessor;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\PriceDiscountInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\SelectedRequestOptionInterface;
use BelVG\MageWorxOptionTemplates\Helper\Data as ParentData;
use BelVG\SaleCountdown\Model\Service\SectionLoader;
use Magento\Catalog\Model\Product\Option as ProductOption;
use Magento\Catalog\Model\Product\Option\Value as ProductOptionValue;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;

class Data extends ParentData
{
    use SelectedOptionProcessor;

    private SelectedRequestOptionInterface $selectedRequestOption;
    private AppState $appState;
    private OptionPriceCalculator $optionPriceCalculator;
    private bool $colorCalculated = false;
    private PriceDiscountInterface $priceDiscountService;
    private GetSelectedOptions $selectedOptions;
    private TaxRateByStoreService $taxRateByStoreService;
    private LayoutPriceCurrency $priceCurrency;

    /**
     * @var array
     */
    protected $productPrices = [];

    /**
     * Data constructor.
     * @param Context $context
     * @param LayoutResourceModel $layoutResource
     * @param SectionLoader $saleCountdown
     * @param StoreManagerInterface $storeManager
     * @param ProductOption $productOption
     * @param TaxRateByStoreService $taxRateByStoreService
     * @param LayoutPriceCurrency $priceCurrency
     * @param AppState $appState
     * @param OptionPriceCalculator $optionPriceCalculator
     * @param PriceDiscountInterface $priceDiscountService
     * @param GetSelectedOptions $selectedOptions
     */
    public function __construct(
        Context $context,
        LayoutResourceModel $layoutResource,
        SectionLoader $saleCountdown,
        StoreManagerInterface $storeManager,
        ProductOption $productOption,
        TaxRateByStoreService $taxRateByStoreService,
        LayoutPriceCurrency $priceCurrency,
        AppState $appState,
        OptionPriceCalculator $optionPriceCalculator,
        PriceDiscountInterface $priceDiscountService,
        GetSelectedOptions $selectedOptions
    ) {
        parent::__construct($context, $layoutResource, $saleCountdown, $storeManager, $productOption);
        $this->taxRateByStoreService = $taxRateByStoreService;
        $this->priceCurrency = $priceCurrency;
        $this->appState = $appState;
        $this->optionPriceCalculator = $optionPriceCalculator;
        $this->priceDiscountService = $priceDiscountService;
        $this->selectedOptions = $selectedOptions;
    }

    public function getOptionsPriceWithDiscount($product, $context, $layoutData)
    {
        $price = $this->getOptionsPrice($product, $context, $layoutData);
        $priceWithDiscount = $this->priceDiscountService->modifier($price, $product);
        return $this->formatPrice($priceWithDiscount);
    }

    public function getOptionsPrice($product, $context, $layoutDataContext)
    {
        $cacheKey = $this->generateKeyCache($product, $context, $layoutDataContext);
        if (\array_key_exists($cacheKey, $this->productPrices)) {
            return $this->productPrices[$cacheKey];
        }
        $selectedOptions = $this->selectedOptions->get();
        $storeId = $context->getStoreManager()->getStore()->getId();
        $width = $layoutDataContext['width'] ?? 0;
        $height = $layoutDataContext['height'] ?? 0;
        $layoutData = $this->layoutResource->getLayoutData(
            $product->getData(LayoutCustomizerHelper::PRODUCT_LAYOUT_ATTR),
            $storeId,
            $width,
            $height
        );
        $width = $layoutData['width'] ?? 0;
        $height = $layoutData['height'] ?? 0;
        $layoutData['__store_id'] = $storeId;
        if ($width * $height === 0) {
            return 0;
        }
        $optionsPrice = 0;
        $options = $product->getOptions();

        //trick to be able to generate the googleshopping feed
        if (empty($options)) {
            $options = $this->productOption->getProductOptionCollection($product);
        }

        if ($options) {
            $this->colorCalculated = false;
            foreach ($options as $option) {
                /** @var ProductOption $option */
                if ($option->getPrice()) {
                    $optionsPrice += $option->getPrice();
                } else {
                    if ($option->getValues()) {
                        $value = $this->getSelectedValue($option, $selectedOptions);
                        $price = $this->getPrice($value, $layoutData['total_price'], $width, $height);
                        $optionsPrice += $price;

                    }
                }
            }
        }

        try {
            $areaCode = $this->appState->getAreaCode();
            if ($areaCode == 'adminhtml') {
                /* Module has ability to generate feed from the admin, in this case we have 2 disabled plugins:
                 *  -> `belvg_layoutcustomizer_option_value_price_add_tax`
                 *  -> `belvg_layoutcustomizer_option_value_price_convert_currency`
                 * that need to be run manually
                 */

                //tax - belvg_layoutcustomizer_option_value_price_add_tax
                $taxRate = $this->taxRateByStoreService->getTaxRateMultiplier($storeId);
                $optionsPrice = $optionsPrice * $taxRate;

                //currency - belvg_layoutcustomizer_option_value_price_convert_currency
                $optionsPrice = $this->priceCurrency->convert($optionsPrice, $storeId);
            }
        } catch (LocalizedException $e) {
            //add logs here
        }

        //option value price converts to currency in plugin "belvg_layoutcustomizer_option_value_price_convert_currency"
        $this->productPrices[$product->getId().'_'.$storeId] = (float)$optionsPrice + $layoutData['total_price'];
        $this->productPrices[$cacheKey] = (float)$optionsPrice + $layoutData['total_price'];
        return $this->productPrices[$cacheKey];
    }

    /**
     * @param $optionValue
     * @param $basePrice
     * @param $width
     * @param $height
     * @return float|int
     */
    public function getPrice($optionValue, $basePrice, $width, $height)
    {
        if ($optionValue->getOption()->getInsideOutsideColor()) {
            $price = 0.0;
            if ($this->colorCalculated === false) {
                $price = $this->optionPriceCalculator->getCustomPrice(
                    $optionValue->getOption()->getProduct(),
                    $optionValue->getOption(),
                    $optionValue,
                    $basePrice
                );
                $this->colorCalculated = true;
            }
        } elseif ($optionValue->getPriceType() === ProductOptionValue::TYPE_PERCENT) {
            $price = $basePrice * ($optionValue->getData(ProductOptionValue::KEY_PRICE) / 100);
        } elseif ($optionValue->getPriceType() === BelVGLayoutOptionPriceTypePricePlugin::VALUE_SQM_PRICE) {
            $price = $optionValue->getPrice() * ($width * $height / self::CM_IN_SQM);
        } else {
            $price = $optionValue->getPrice();
        }
        return $price;
    }

    private function generateKeyCache($product, $context, $layoutDataContext)
    {
        $storeId = $context->getStoreManager()->getStore()->getId();
        $layoutDataKey = \var_export($layoutDataContext, true);
        return $product->getSku().$layoutDataKey.$storeId;
    }
}
