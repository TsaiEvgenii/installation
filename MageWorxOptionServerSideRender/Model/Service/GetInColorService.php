<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\InsideOutsideColorPrice\Model\OptionPriceCalculator;
use BelVG\MageWorxOptionServerSideRender\Api\Data\ColorDescriptionInterface;
use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface;
use Magento\Framework\Api\ObjectFactory;

class GetInColorService
{
    use SelectedOptionProcessor;
    const TYPE = OptionPriceCalculator::INSIDE;
    private ObjectFactory $objectFactory;

    /**
     * GetInColorService constructor.
     * @param ObjectFactory $objectFactory
     */
    public function __construct(ObjectFactory $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }
    /**
     * @param SelectedOptionInterface[] $selectedOptions
     * @param \Magento\Catalog\Model\Product\Option[] $options
     */
    public function get(iterable $options, iterable $selectedOptions = []): ColorDescriptionInterface
    {
        $inSideColorOption = $this->getOption($options);
        if ($inSideColorOption && $value = $this->getValue($inSideColorOption, $selectedOptions)) {
                return  $this->objectFactory->create(ColorDescriptionInterface::class, ['data'=>
                    [ColorDescriptionInterface::COLOR_TYPE=>static::TYPE,
                        ColorDescriptionInterface::IS_DEFAULT=>$value->getData('is_default'),
                        ColorDescriptionInterface::TITLE => $value->getTitle()]
                ]);
        }

        return $this->objectFactory->create(ColorDescriptionInterface::class, ['data'=>[]]);
    }


    private function getOption(iterable $options)
    {
        $options = \array_filter($options, fn($option)=>$option->getData('inside_outside_color') === static::TYPE);
        return reset($options);
    }
}
