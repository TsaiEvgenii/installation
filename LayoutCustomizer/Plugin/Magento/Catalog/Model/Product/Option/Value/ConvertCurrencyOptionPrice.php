<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Plugin\Magento\Catalog\Model\Product\Option\Value;

use BelVG\LayoutCustomizer\Model\PriceCurrency as LayoutPriceCurrency;
use Magento\Catalog\Model\Product\Option\Value as Original;
use Magento\Catalog\Model\Product\Option\Value as ProductOptionValue;
use Magento\Store\Model\StoreManagerInterface;

class ConvertCurrencyOptionPrice
{
    /** @var LayoutPriceCurrency  */
    private $priceCurrency;

    /** @var StoreManagerInterface  */
    private $storeManager;

    /**
     * ConvertCurrencyOptionPrice constructor.
     * @param LayoutPriceCurrency $priceCurrency
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        LayoutPriceCurrency $priceCurrency,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManagerInterface;
    }

    /**
     * Layout customizer currency conversions
     * https://app.asana.com/0/1177395662263354/1197504336481685/f
     *
     * @param ProductOptionValue $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetPrice(
        Original $subject,
        $result
    ) {
        if ($subject->getPriceType() === ProductOptionValue::TYPE_PERCENT) {
            return $result;
        }

        $storeId = $this->storeManager->getStore()->getId();

        return $this->priceCurrency->convert($result, $storeId);
    }
}
