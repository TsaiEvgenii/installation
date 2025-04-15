<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Ui\DataProvider\Group\Form\Modifier;


use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as CustomOptionsOriginal;
use Magento\Framework\Stdlib\ArrayManager;
use MageWorx\OptionTemplates\Ui\DataProvider\Group\Form\Modifier\CustomOptions;

class MadeInDenmarkPriceOptionValue extends AbstractModifier
{
    public function __construct(
        protected ArrayManager $arrayManager,
        protected LocatorInterface $locator,
    ){

    }

    public function modifyData(array $data): array
    {
        if ($productData = ($data[$this->locator->getProduct()->getId()] ?? false)) {
            $options = $productData[CustomOptions::DATA_SOURCE_DEFAULT][CustomOptionsOriginal::GRID_OPTIONS_NAME];
            foreach ($options as $optionKey => $option) {
                $optionValues = $option['values'] ?? [];
                foreach($optionValues as $valueKey => $value){
                    $path = [
                        $this->locator->getProduct()->getId(),
                        CustomOptions::DATA_SOURCE_DEFAULT,
                        CustomOptionsOriginal::GRID_OPTIONS_NAME,
                        $optionKey,
                        'values',
                        $valueKey,
                        'made_in_denmark_price'
                    ];
                    $data = $this->arrayManager->replace($path, $data, $this->formatPrice($value['made_in_denmark_price']));
                }
            }
        }
        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}