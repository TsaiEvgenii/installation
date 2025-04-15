<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Helper;

use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout as LayoutResourceModel;
use BelVG\LayoutOptionPriceType\Plugin\Magento\Catalog\Model\Config\Source\Product\Options\PricePlugin as BelVGLayoutOptionPriceTypePricePlugin;
use BelVG\SaleCountdown\Model\Service\SectionLoader as SaleCountdownSectionLoader;
use Magento\Catalog\Model\Product\Option as ProductOption;
use Magento\Catalog\Model\Product\Option\Value as ProductOptionValue;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package BelVG\MageWorxOptionTemplates\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CM_IN_SQM = 10000;

    private array $productPrices = [];
    private array $discountByStore = [];

    public function __construct(
        Context $context,
        protected LayoutResourceModel $layoutResource,
        protected SaleCountdownSectionLoader $saleCountdown,
        protected StoreManagerInterface $storeManager,
        protected ProductOption $productOption,
    ) {
        parent::__construct($context);
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
        if ($optionValue->getPriceType() === ProductOptionValue::TYPE_PERCENT) {
            $price = $basePrice * ($optionValue->getData(ProductOptionValue::KEY_PRICE) / 100);
        } elseif ($optionValue->getPriceType() === BelVGLayoutOptionPriceTypePricePlugin::VALUE_SQM_PRICE) {
            $price = $optionValue->getData(ProductOptionValue::KEY_PRICE) * ($width * $height / self::CM_IN_SQM);
        } else {
            $price = $optionValue->getPrice();
        }
        return $price;
    }

    /**
     * @param $product
     * @param $storeId
     * @param $layoutData
     * @return float|int|mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDefaultOptionsPriceWithDiscount(
        $product,
        $storeId,
        $layoutData = null
    ) {
        $price = $this->getDefaultOptionsPrice($product, $storeId, $layoutData);
        $discountPercent = $this->getDiscountPercentForStoreId($storeId);
        if ($discountPercent) {
            $price = $this->formatPrice($price - ($price * $discountPercent / 100));
        }

        return $price;
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price)
    {
        return number_format($price, 2, '.', '');
    }

    /**
     * Method is needed to let pluginize it
     *
     * @param $product
     * @param $optionsPrice
     * @param $storeId
     * @return mixed
     */
    public function beforeSetOptionPrices(
        $product,
        $optionsPrice,
        $storeId
    ) {
        return $optionsPrice;
    }

    /**
     * @param $product
     * @param $storeId
     * @param null $layoutData
     * @return float|int|mixed
     */
    public function getDefaultOptionsPrice(
        $product,
        $storeId,
        $layoutData = null
    ) {
        if (isset($this->productPrices[$product->getId().'_'.$storeId])) {
            return $this->productPrices[$product->getId().'_'.$storeId];
        }

        if (!$layoutData) {
            $layoutData = $this->layoutResource->getLayoutData($product->getData(LayoutCustomizerHelper::PRODUCT_LAYOUT_ATTR), $storeId);
        }

        $width = $layoutData['width'] ?? 0;
        $height = $layoutData['height'] ?? 0;
        if($width * $height === 0){
            return 0;
        }
        $optionsPrice = 0;
        $options = $product->getOptions();

        //trick to be able to generate the googleshopping feed
        if (empty($options)) {
            $options = $this->productOption->getProductOptionCollection($product);
        }

        if ($options) {
            foreach ($options as $optionId => $option) {
                /** @var ProductOption $option */
                if ($option->getPrice()) {
                    $optionsPrice += $option->getPrice();
                } else {
                    if ($option->getValues()) {
                        foreach ($option->getValues() as $value) {
                            /** @var ProductOptionValue $value */
                            if ($value->getIsDefault()) {
                                $price = $this->getPrice($value, $layoutData['total_price'], $width, $height);
                                $optionsPrice += $price;
                            }
                        }
                    }
                }
            }
        }

        $optionsPrice = $this->beforeSetOptionPrices($product, $optionsPrice, $storeId);

        //option value price converts to currency in plugin "belvg_layoutcustomizer_option_value_price_convert_currency"
        $this->productPrices[$product->getId().'_'.$storeId] = (float)$optionsPrice + (float)$layoutData['total_price'];

        return $this->productPrices[$product->getId().'_'.$storeId];
    }

    /**
     * @param int $storeId
     * @return float|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getDiscountPercentForStoreId(int $storeId) :?float {
        if (isset($this->discountByStore[$storeId])) {
            return $this->discountByStore[$storeId];
        }

        $this->discountByStore[$storeId] = null;
        $websiteId = (int)$this->storeManager->getStore($storeId)->getWebsiteId();
        $section = $this->saleCountdown->loadSectionData([
            'website_id' => $websiteId
        ]);
        if (isset($section['percent'])) {
            $this->discountByStore[$storeId] = (float)$section['percent'];
        }

        return $this->discountByStore[$storeId];
    }
}
