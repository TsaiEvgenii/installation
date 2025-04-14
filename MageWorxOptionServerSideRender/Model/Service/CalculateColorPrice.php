<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\InsideOutsideColorPrice\API\OptionPriceCalculator as OptionPriceCalculatorInterface;
use BelVG\InsideOutsideColorPrice\Model\OptionPriceCalculator;
use BelVG\MageWorxOptionServerSideRender\Api\Data\ColorDescriptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ColorfulWindowTypeProcessorInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\GetProductLayoutInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Psr\Log\LoggerInterface;

class CalculateColorPrice implements OptionPriceCalculatorInterface
{
    private GetSelectedOptions $selectedOptions;
    private GetInColorService $getInColorService;
    private GetOutColorService $getOutColorService;
    private ColorfulWindowTypeProcessorInterface $colorfulWindowType;
    private LoggerInterface $logger;
    private GetProductLayoutInterface $productLayout;
    private OptionPriceCalculator $optionPriceCalculator;

    /**
     * CalculateColorPrice constructor.
     * @param GetSelectedOptions $selectedOptions
     * @param GetInColorService $getInColorService
     * @param GetOutColorService $getOutColorService
     * @param ColorfulWindowTypeProcessorInterface $colorfulWindowType
     * @param LoggerInterface $logger
     * @param GetProductLayoutInterface $productLayout
     * @param \BelVG\InsideOutsideColorPrice\Model\OptionPriceCalculator $optionPriceCalculator
     */
    public function __construct(
        GetSelectedOptions $selectedOptions,
        GetInColorService $getInColorService,
        GetOutColorService $getOutColorService,
        ColorfulWindowTypeProcessorInterface $colorfulWindowType,
        LoggerInterface $logger,
        GetProductLayoutInterface $productLayout,
        OptionPriceCalculator $optionPriceCalculator
    ) {
        $this->selectedOptions = $selectedOptions;
        $this->getInColorService = $getInColorService;
        $this->getOutColorService = $getOutColorService;
        $this->colorfulWindowType = $colorfulWindowType;
        $this->logger = $logger;
        $this->productLayout = $productLayout;
        $this->optionPriceCalculator = $optionPriceCalculator;
    }

    public function getCustomPrice($product, Option $option, Value $optionValue, $basePrice, $additionalParams = [])
    {
        if($product) {
            $selectedOptions = $this->selectedOptions->get();
            $inColorDescription = $this->getInColorService->get($product->getOptions(), $selectedOptions);
            $outColorDescription = $this->getOutColorService->get($product->getOptions(), $selectedOptions);
            $colorfulType = $this->colorfulWindowType->getType($inColorDescription, $outColorDescription);
            if ($this->hasEmptyColor($inColorDescription, $outColorDescription)) {
                $this->log($product, $inColorDescription, $outColorDescription);
            }
            $layout = $this->productLayout->get($product);
            return $this->optionPriceCalculator->resolvePrice($colorfulType, $basePrice, $layout);
        }
    }

    private function hasEmptyColor(ColorDescriptionInterface $inColorDescription, ColorDescriptionInterface $outColorDescription)
    {
        return $inColorDescription->getTitle() === '' || $outColorDescription->getTitle() === '';
    }

    private function log($product, ColorDescriptionInterface $inColorDescription, ColorDescriptionInterface $outColorDescription)
    {
        $this->logger->warning('Some of color is empty', ['in_color'=>(array)$inColorDescription,
            'out_color'=>(array)$outColorDescription,
            'product_id'=>$product->getId()]);
    }
}
