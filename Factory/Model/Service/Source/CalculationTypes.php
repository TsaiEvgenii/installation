<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Service\Source;

enum CalculationTypes: int
{
    /**
     * Used week = Default Delivery Time + current week
     */
    case DYNAMIC = 0;

    /**
     * Used week = provided week
     */
    case STATIC = 1;
}
