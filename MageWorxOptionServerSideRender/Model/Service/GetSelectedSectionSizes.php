<?php
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\LayoutCustomizer\Helper\Data;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\SelectedRequestOptionInterface;
use Magento\Framework\App\RequestInterface;

class GetSelectedSectionSizes
{

    const PARAMETER_NAME = 'section_sizes';

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
        $section_sizes_option = $this->layoutCustomizerData->getSectionsSizesOption();
        $sectionSizes = 0;
        foreach ($this->selectedOptions as $option) {
            if ($option->getOptionKey() == self::PARAMETER_NAME) {
                $sectionSizes = $option->getObjectValue()->getValue();
                break;
            }
            if ($this->layoutCustomizerData->matchDbOptionWithConfig($option->getOptionId(), $section_sizes_option)) {
                $sectionSizes = $option->getObjectValue()->getValue();
                break;
            }
        }
        return (string)$sectionSizes;
    }
}
