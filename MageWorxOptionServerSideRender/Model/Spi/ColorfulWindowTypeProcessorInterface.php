<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Spi;

use BelVG\MageWorxOptionServerSideRender\Api\Data\ColorDescriptionInterface;

interface ColorfulWindowTypeProcessorInterface
{
    /**
     * @param ColorDescriptionInterface $inColorDescription
     * @param ColorDescriptionInterface $outColorDescription
     * @return string
     */
    public function getType(ColorDescriptionInterface $inColorDescription, ColorDescriptionInterface $outColorDescription) :string;
}
