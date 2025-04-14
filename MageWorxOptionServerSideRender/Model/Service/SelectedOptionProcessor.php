<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Dto\SelectedOption;
use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;
use Magento\Framework\DataObject;

trait SelectedOptionProcessor
{

    protected function isSelectedOption($option, iterable $selectedOptions)
    {
        return (bool)$this->getSelectedOption($option, $selectedOptions);
    }

    private function getSelectedOption($option, $selectedOptions) : SelectedOptionInterface
    {
        if (is_object($selectedOptions)) {
            $selectedOptions = $selectedOptions->get();
        }
        $selectedOption = \array_filter($selectedOptions, static fn ($selectedOption)=> $selectedOption->getOptionId() === (int)$option->getId());
        if (\count($selectedOption) === 0) {
            $selectedOption = \array_filter($selectedOptions, static fn ($selectedOption)=> $selectedOption->getOptionKey() === $option->getOptionKey());
            if (\count($selectedOption) === 0) {
                $defaultValue = $this->getDefaultValue($option);
                return new SelectedOption(['option_id' => $option->getId(), 'value' => new SelectedOptionValue(['value' => $defaultValue->getId()])]);
            }
        }
        return \reset($selectedOption);
    }

    private function getDefaultValue($option)
    {
        $value = new DataObject();
        foreach ($option->getValues() ?? [] as $value) {
            if ($value->getData('is_default')) {
                return $value;
            }
        }
        return $value;
    }

    protected function getSelectedValue($option, iterable $selectedOptions)
    {
        $selectedOption = $this->getSelectedOption($option, $selectedOptions);
        if ($option->getValues() === null) {
            return $selectedOption->getValue();
        }
        foreach ($option->getValues() ?? [] as $value) {
            if ($selectedOption->getValue() === (string)$value->getId() || $selectedOption->getObjectValue()->getValue() === (string)$value->getOptionTypeKey()) {
                return $value;
            }
        }
        return $this->getDefaultValue($option);
    }

    protected function getValue($option, iterable $selectedOptions)
    {
        return $this->getSelectedValue($option, $selectedOptions);
    }
}
