<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model;

use Magento\Framework\Api\AbstractSimpleObject;

class SelectedOptionValue extends AbstractSimpleObject
{
    const VALUE = 'value';

    public function getValue() :string
    {
        return (string)$this->_get(self::VALUE);
    }

    public function setValue($newData)
    {
        $this->setData(self::VALUE, $newData);
    }

    public function __toString()
    {
        \preg_match($this->getSpecialColorValuePattern(), $this->getValue(), $matches);
        return (string)$matches['option_id'];
    }

    public function getSpecialColorValuePattern()
    {
        $delimeter = '$';
        $optionIdPattern = '(?<option_id>(^\d*))';
        $ralColorPattern = ':?(?<ral_value>(.*))?';
        return $delimeter . $optionIdPattern . $ralColorPattern . $delimeter;
    }

    public function getRalValue()
    {
        \preg_match($this->getSpecialColorValuePattern(), $this->getValue(), $matches);
        return (string)$matches['ral_value'];
    }
    public function getPureValue(): string
    {
        return $this->getValue();
    }
}
