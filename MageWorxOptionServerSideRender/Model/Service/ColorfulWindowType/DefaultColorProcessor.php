<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

class DefaultColorProcessor extends AbstractColorProcessor
{
    protected function isSpecifiedType($inColorDescription, $outColorDescription)
    {
        return $inColorDescription->isDefault() && $outColorDescription->isDefault();
    }
}
