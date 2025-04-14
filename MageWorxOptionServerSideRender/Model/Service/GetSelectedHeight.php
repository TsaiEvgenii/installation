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

class GetSelectedHeight
{

    const PARAMETER_NAME = 'height';

    private Data $layoutCustomizerData;
    /**
     * @var \BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface[]|iterable
     */
    private iterable $selectedOptions;

    /**
     * GetSelectedWidth constructor.
     * @param GetSelectedOptions $selectedRequestOption
     * @param Data $layoutCustomizerData
     */
    public function __construct(
        GetSelectedOptions $selectedOptions,
        Data $layoutCustomizerData
    ) {
        $this->selectedOptions = $selectedOptions;
        $this->layoutCustomizerData = $layoutCustomizerData;
    }

    public function get() :string
    {
        $overall_height = $this->layoutCustomizerData->getOverallHeightOption();
        $height = 0;
        foreach ($this->selectedOptions as $option) {
            if ($option->getOptionKey() == self::PARAMETER_NAME) {
                $height = $option->getObjectValue()->getValue();
                break;
            }
            if ($this->layoutCustomizerData->matchDbOptionWithConfig($option->getOptionId(), $overall_height)) {
                $height = $option->getObjectValue()->getValue();
                break;
            }
        }
        return (string)$height;
    }
}
