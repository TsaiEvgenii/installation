<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

class EmptyColor extends AbstractColorProcessor
{
    const TYPE = 'default';

    protected function isSpecifiedType($inColorDescription, $outColorDescription)
    {
        return $inColorDescription->getTitle() === '' || $outColorDescription->getTitle() === '';
    }
}
