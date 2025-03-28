<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\SaveCart\Total\Modifier;

use BelVG\SaveCartTotals\Api\Totals\Modifiers\ModifierInterface;
use MageKey\SaveCart\Model\Item\Cart as SavedItemCart;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Discount implements ModifierInterface
{
    /** @var TimezoneInterface */
    protected $timezone;

    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    /**
     * @param SavedItemCart $cart
     * @param iterable $totals
     * @return iterable
     */
    public function modifyCartTotals(
        SavedItemCart $cart,
        iterable $totals
    ): iterable {
        $totals = array_merge(
            [
                'b2b_discount' => $cart->getData('b2b_discount'),
                'b2b_discount_percent' => $cart->getData('b2b_discount_percent')
            ],
            $totals
        );

        return $totals;
    }
}
