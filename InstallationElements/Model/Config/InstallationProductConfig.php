<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Config;


class InstallationProductConfig
{
    private const ATTRIBUTE_SET_NAME = 'Installation';
    public const SKU = 'INSTALLATION_SERVICE';
    private const NAME = 'Professional installation service';
    const PRODUCT_TYPE = 'installation_product_type';

    /**
     * @return string
     */
    public function getAttributeSetName(): string
    {
        return self::ATTRIBUTE_SET_NAME;
    }

    /**
     * @return string
     */
    public function getProductSku(): string
    {
        return self::SKU;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getProductType(): string
    {
        return self::PRODUCT_TYPE;
    }
}