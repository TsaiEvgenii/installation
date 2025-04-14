<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Api\Data;

interface ColorDescriptionInterface
{
    const TITLE = 'title';
    const IS_DEFAULT = 'is_default';
    const COLOR_TYPE = 'color_type';
    /**
     * @return string
     */
    public function getTitle():string;

    /**
     * @return bool
     */
    public function isDefault() :bool;

    /**
     * @return string
     */
    public function getColorType() :string;
}
