<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

trait ComparisonTrait
{
    private function isNoDefault($inColorDescription, $outColorDescription)
    {
        return ($inColorDescription->isDefault()===false && $outColorDescription->isDefault() === false) === true;
    }

    private function isSameColor($inColorDescription, $outColorDescription)
    {
        return \strcmp($inColorDescription->getTitle(), $outColorDescription->getTitle()) === 0;
    }
}
