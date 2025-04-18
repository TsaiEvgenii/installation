<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\OrderUpgrader\Api\Webapi;

interface UpgradeQuoteInterface
{

    /**
     * @param string $cartId
     * @param string $storeId
     * @param \BelVG\OrderUpgrader\Api\Data\UpgradeParameterInterface[] $parameters
     * @return void
     */
    public function upgradeQuote(
        $cartId,
        $storeId,
        $parameters
    ) :void ;

    /**
     * @param string $cartId
     * @param string $storeId
     * @param \BelVG\OrderUpgrader\Api\Data\UpgradeParameterInterface[] $parameters
     * @return void
     */
    public function upgradeQuoteForGuest(
        $cartId,
        $storeId,
        $parameters
    ) :void ;

}