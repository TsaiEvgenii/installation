<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2024
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Plugin\Block\Product\View;

use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use Magento\Catalog\Block\Product\View\Options;
use Magento\Framework\App\ObjectManager;

class OptionsPlugin
{
    private GetSelectedOptions $getSelectedOptions;

    public function __construct()
    {
        $this->getSelectedOptions = ObjectManager::getInstance()->get(GetSelectedOptions::class);
    }

    public function afterGetCacheKeyInfo(Options $subject, $result)
    {
        $additionalCacheKeyInfo = [];

        foreach ($this->getSelectedOptions as $selectedOption) {
            if ($selectedOption->getOptionId() !== 0) {
                $additionalCacheKeyInfo[$selectedOption->getOptionId()] = $selectedOption->getOptionId() . '-'
                    . $selectedOption->getValue();
            } elseif ($selectedOption->getOptionKey()) {
                $additionalCacheKeyInfo[$selectedOption->getOptionKey()] = $selectedOption->getOptionKey() . '-'
                    . $selectedOption->getObjectValue()->getPureValue();
            }
        }
        ksort($additionalCacheKeyInfo);
        $result = array_merge($result, $additionalCacheKeyInfo);
        $this->addProductToCacheKey($result, $subject);
        $this->addProductCacheTags($subject);
        return $result;
    }

    private function addProductToCacheKey(&$result, $subject)
    {
        $product = $subject->getProduct();
        $result['product_id'] = $product->getId();
        $result['product_sku'] = $product->getSku();
    }

    private function addProductCacheTags($subject)
    {
        $productTags = $subject->getProduct()->getCacheTags();
        $tags = \array_merge((array)$subject->getCacheTags(), (array)$productTags);
        $subject->setCacheTags(\array_unique($tags));
    }
}