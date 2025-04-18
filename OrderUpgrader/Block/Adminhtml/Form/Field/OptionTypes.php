<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

declare(strict_types=1);

namespace BelVG\OrderUpgrader\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Dynamic form for option types in admin configuration
 */
class OptionTypes extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('code', [
            'label'    => __('Option Code'),
            'class'    => 'required-entry',
            'renderer' => null,
        ]);

        $this->addColumn('label', [
            'label'    => __('Display Label'),
            'class'    => 'required-entry',
            'renderer' => null,
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Option Type');
    }
}