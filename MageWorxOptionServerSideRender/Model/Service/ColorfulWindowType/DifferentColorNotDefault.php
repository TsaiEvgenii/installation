<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

use BelVG\MageWorxOptionServerSideRender\Api\Data\ColorDescriptionInterface;

class DifferentColorNotDefault extends AbstractColorProcessor
{
    use ComparisonTrait;

    const TYPE = 'both_diff';

    /**
     * @param  ColorDescriptionInterface $inColorDescription
     * @param ColorDescriptionInterface $outColorDescription
     */
    protected function isSpecifiedType($inColorDescription, $outColorDescription)
    {
        return $this->isNoDefault($inColorDescription, $outColorDescription)
            && $this->isDiffColor($inColorDescription, $outColorDescription);
    }

    private function isDiffColor($inColorDescription, $outColorDescription)
    {
        return $this->isSameColor($inColorDescription, $outColorDescription) === false;
    }
}
