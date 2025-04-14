<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Plugin\Magento\Catalog\Model\Product\Option\Value;

use BelVG\LayoutCustomizer\Model\Service\TaxRateByStoreService;
use Magento\Catalog\Model\Product\Option\Value as Original;
use Magento\Catalog\Model\Product\Option\Value as ProductOptionValue;
use Magento\Store\Model\StoreManagerInterface;

class AddTaxToOptionPrice
{
    private $taxRateByStoreService;
    private $storeManager;

    public function __construct(
        TaxRateByStoreService $taxRateByStoreService,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->taxRateByStoreService = $taxRateByStoreService;
        $this->storeManager = $storeManagerInterface;
    }

    /**
     * All prices should be without Tax
     * https://app.asana.com/0/1177395662263354/1177969533611915
     *
     * @param Original $subject
     * @param $result
     * @return float|int
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
        $taxRate = $this->taxRateByStoreService->getTaxRateMultiplier($storeId);

        return $result * $taxRate;
    }
}
