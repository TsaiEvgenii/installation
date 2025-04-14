<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

class InSideDefault extends AbstractColorProcessor
{
    const  TYPE = 'out_other_white';

    protected function isSpecifiedType($inColorDescription, $outColorDescription)
    {
        return $inColorDescription->isDefault() === true && $outColorDescription->isDefault() === false;
    }
}
