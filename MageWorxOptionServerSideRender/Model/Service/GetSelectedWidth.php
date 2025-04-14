<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\LayoutCustomizer\Helper\Data;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\SelectedRequestOptionInterface;
use Magento\Framework\App\RequestInterface;

class GetSelectedWidth
{

    const PARAMETER_NAME = 'width';

    private Data $layoutCustomizerData;
    /**
     * @var \BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface[]|iterable
     */
    private iterable $selectedOptions;

    /**
     * GetSelectedWidth constructor.
     * @param GetSelectedOptions $selectedOptions
     * @param Data $layoutCustomizerData
     */
    public function __construct(
        GetSelectedOptions $selectedOptions,
        Data $layoutCustomizerData
    ) {
        $this->layoutCustomizerData = $layoutCustomizerData;
        $this->selectedOptions = $selectedOptions;
    }

    public function get() :string
    {
        $overall_width = $this->layoutCustomizerData->getOverallWidthOption();
        $width = 0;
        foreach ($this->selectedOptions as $option) {
            if ($option->getOptionKey() == self::PARAMETER_NAME) {
                $width = $option->getObjectValue()->getValue();
                break;
            }
            if ($this->layoutCustomizerData->matchDbOptionWithConfig($option->getOptionId(), $overall_width)) {
                $width = $option->getObjectValue()->getValue();
                break;
            }
        }
        return (string)$width;
    }
}
