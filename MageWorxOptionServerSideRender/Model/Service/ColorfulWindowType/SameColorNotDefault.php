<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

class SameColorNotDefault extends AbstractColorProcessor
{
    use ComparisonTrait;

    const TYPE = 'both_same';
    protected function isSpecifiedType($inColorDescription, $outColorDescription)
    {
        return $this->isNoDefault($inColorDescription, $outColorDescription)
            && $this->isSameColor($inColorDescription, $outColorDescription);
    }
}
