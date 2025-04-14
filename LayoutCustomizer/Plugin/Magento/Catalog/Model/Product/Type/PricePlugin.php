<?php


namespace BelVG\LayoutCustomizer\Plugin\Magento\Catalog\Model\Product\Type;


class PricePlugin
{
    public function afterGetPrice(
        \Magento\Catalog\Model\Product\Type\Price $subject,
        $result,
        $product
    ) {
        if (is_null($result)) {
            return (float)$result;
        }

        return $result;
    }
}
