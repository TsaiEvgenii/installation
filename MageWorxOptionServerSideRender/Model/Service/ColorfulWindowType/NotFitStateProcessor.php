<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

use BelVG\MageWorxOptionServerSideRender\Exception\NotFitColorfulTypeException;

class NotFitStateProcessor extends AbstractColorProcessor
{
    protected function isSpecifiedType($inColorDescription, $outColorDescription)
    {
        throw new NotFitColorfulTypeException(\json_encode(['in_color'=>(array)$inColorDescription,
            'out_color'=>(array)$outColorDescription]));
    }
}
