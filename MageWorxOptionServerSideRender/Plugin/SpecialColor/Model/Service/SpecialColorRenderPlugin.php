<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Plugin\SpecialColor\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedSpecialColor;
use BelVG\MageWorxSpecialColor\Model\Service\Renderer\AfterTitle\SpecialColorRenderer;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;

class SpecialColorRenderPlugin
{
    /**
     * @var GetSelectedSpecialColor
     */
    private GetSelectedSpecialColor $selectedSpecialColor;

    /**
     * SpecialColorRenderPlugin constructor.
     * @param GetSelectedSpecialColor $selectedSpecialColor
     */
    public function __construct(GetSelectedSpecialColor $selectedSpecialColor)
    {
        $this->selectedSpecialColor = $selectedSpecialColor;
    }

    public function afterGetSpecialColorSelectedOption(
        SpecialColorRenderer $subject,
        $result,
        ProductCustomOptionValuesInterface $optionValue
    ) {
        if ($result === null) {
            $option = $optionValue->getOption();
            $selectedColor = $this->selectedSpecialColor->get($option);
            $result = $selectedColor->getIdentifier();
        }
        return $result;
    }
}
