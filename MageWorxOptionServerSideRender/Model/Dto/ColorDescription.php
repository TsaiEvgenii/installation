<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Dto;

use BelVG\MageWorxOptionServerSideRender\Api\Data\ColorDescriptionInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class ColorDescription extends AbstractSimpleObject implements ColorDescriptionInterface
{
    public function getTitle(): string
    {
        return (string)$this->_get(self::TITLE);
    }

    public function isDefault(): bool
    {
        return (bool)$this->_get(self::IS_DEFAULT);
    }

    public function getColorType(): string
    {
        return (string)$this->_get(self::COLOR_TYPE);
    }
}
