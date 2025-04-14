<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\InsideOutsideColorPrice\Model\OptionPriceCalculator;

class GetOutColorService extends GetInColorService
{
    const TYPE = OptionPriceCalculator::OUTSIDE;
}
