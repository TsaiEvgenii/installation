<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Api\Webapi;

use BelVG\InstallationElements\Api\Data\InstallationInterface;

interface AddInstallationProductInterface
{
    /**
     * @param string                $cartId
     * @param string                $storeId
     * @param InstallationInterface $installationData
     *
     * @return void
     */
    public function addProduct(
        string $cartId,
        string $storeId,
        InstallationInterface $installationData
    ): void;

    /**
     * @param string                $cartId
     * @param string                $storeId
     * @param InstallationInterface $installationData
     *
     * @return void
     */
    public function addProductForGuest(
        string $cartId,
        string $storeId,
        InstallationInterface $installationData
    ): void;

}