<?php
/*
 *  @package Vinduesgrossisten
 *   * @author  Tsai<tsai.evgenii@belvg.com>
 *   * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model\Config\Backend;


/**
 * Class CsvFileType
 *
 * @package BelVG\MageWorxOptionTemplates\Model\Config\Backend
 */
class CsvFileType extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['csv'];
    }
}
