<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Dto;

use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface as SelectedOptionInterfaceAlias;
use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;
use Magento\Framework\Api\AbstractSimpleObject;

class SelectedOption extends AbstractSimpleObject implements SelectedOptionInterfaceAlias
{
    public function getOptionId(): int
    {
        return (int)$this->_get(self::OPTION_ID);
    }

    public function getOptionKey(): string
    {
        return (string)$this->_get(self::OPTION_KEY);
    }

    public function getValue(): string
    {
        return (string)$this->_get(self::VALUE);
    }


    public function getObjectValue() : ?SelectedOptionValue
    {
        return $this->_get(self::VALUE);
    }
}
