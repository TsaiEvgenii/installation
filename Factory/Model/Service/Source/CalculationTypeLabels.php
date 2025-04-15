<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Service\Source;

enum CalculationTypeLabels: string
{
    /**
     * Used week = Default Delivery Time + current week
     */
    case DYNAMIC = 'Dynamic';

    /**
     * Used week = provided week
     */
    case STATIC = 'Static';
}
