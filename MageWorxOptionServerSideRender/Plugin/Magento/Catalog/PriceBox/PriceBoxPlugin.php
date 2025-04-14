<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Plugin\Magento\Catalog\PriceBox;

use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;

class PriceBoxPlugin
{
    private GetSelectedOptions $selectedOptions;

    /**
     * PriceBoxPlugin constructor.
     * @param GetSelectedOptions $selectedOptions
     */
    public function __construct(GetSelectedOptions $selectedOptions)
    {
        $this->selectedOptions = $selectedOptions;
    }
    public function afterGetCacheKeyInfo($subject, $result)
    {
        $selectedOptions = $this->selectedOptions->get();
        $key = '';
        foreach ($selectedOptions as $selectedOption) {
            $key = $key.'option-'.$selectedOption->getOptionId().'-value'.$selectedOption->getValue();
        }
        $result['options_data']= $key;
        return $result;
    }
}
