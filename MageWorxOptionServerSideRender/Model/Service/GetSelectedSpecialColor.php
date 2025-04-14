<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;
use BelVG\MageWorxSpecialColor\Api\Data\SpecialColorRalCodeInterface;
use BelVG\MageWorxSpecialColor\Model\Data\SpecialColorRalCode;
use BelVG\MageWorxSpecialColor\Model\Service\SpecialColor;
use Magento\Catalog\Model\Product\Option;

class GetSelectedSpecialColor
{
    use SelectedOptionProcessor;

    /**
     * @var SpecialColor
     */
    private SpecialColor $specialColor;
    /**
     * @var GetSelectedOptions
     */
    private GetSelectedOptions $selectedOptions;

    /**
     * GetSelectedSpecialColor constructor.
     * @param SpecialColor $specialColor
     * @param GetSelectedOptions $selectedOptions
     */
    public function __construct(
        SpecialColor $specialColor,
        GetSelectedOptions $selectedOptions
    ) {
        $this->specialColor = $specialColor;
        $this->selectedOptions = $selectedOptions;
    }

    public function get(Option $option) : SpecialColorRalCodeInterface
    {
        /**
         * @var SelectedOptionInterface $selectedValueObject
         */
        $selectedValueObject = $this->getSelectedOption($option, $this->selectedOptions);
        $selectedSpecialColorRal = $option->getProduct()->getPreconfiguredValues()->getData('special_color_ral/' . $selectedValueObject->getObjectValue()->getValue());
        if ($selectedSpecialColorRal) {
            $selectedValueObject->getObjectValue()->setValue($selectedValueObject->getObjectValue()->getValue() . ':' . $selectedSpecialColorRal);
        }
        foreach ($this->specialColor->getSpecialColors() as $specialColor) {
            if ($this->isSelected($specialColor->getData('identifier'), $selectedValueObject->getObjectValue())) {
                return $specialColor->getDataModel();
            }
        }
        return new SpecialColorRalCode();
    }

    private function isSelected($ralColor, ?SelectedOptionValue $objectValue)
    {
        if ($objectValue === null) {
            return false;
        }
        return $ralColor === $objectValue->getRalValue();
    }
}
