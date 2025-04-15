<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class CurrentWeekViewModel implements ArgumentInterface
{
    /**
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        private readonly TimezoneInterface $timezone
    ) {

    }

    /**
     * @return string
     */
    public function getWeekNumber(): string
    {
        return $this->timezone->date()->format('W');
    }
}
