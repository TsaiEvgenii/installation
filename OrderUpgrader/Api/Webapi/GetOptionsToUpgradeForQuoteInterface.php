<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\OrderUpgrader\Api\Webapi;

use BelVG\OrderUpgrader\Api\Data\OptionsToUpgradeInterface;

interface GetOptionsToUpgradeForQuoteInterface
{
    /**
     * @param string $cartId
     *
     * @return OptionsToUpgradeInterface
     */
    public function getOptions(
        $cartId
    ) : OptionsToUpgradeInterface ;

    /**
     * @param string $cartId
     *
     * @return OptionsToUpgradeInterface
     */
    public function getOptionsForGuest(
        $cartId
    ) : OptionsToUpgradeInterface ;

}