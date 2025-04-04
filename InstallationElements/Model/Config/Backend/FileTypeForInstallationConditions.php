<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Config\Backend;



class FileTypeForInstallationConditions extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return string[]
     */
    protected function _getAllowedExtensions(): array
    {
        return ['pdf'];
    }
}