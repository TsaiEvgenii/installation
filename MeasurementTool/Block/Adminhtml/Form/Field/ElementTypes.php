<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block\Adminhtml\Form\Field;


use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class ElementTypes extends AbstractFieldArray
{
    protected function _prepareToRender(): void
    {
        $this->addColumn('element_type_code', [
            'label' => __('Code'),
            'class' => 'required-entry no-whitespace'
        ]);
        $this->addColumn('element_type_label', [
            'label' => __('Label'),
            'class' => 'required-entry'
        ]);
        $this->addColumn('element_type_url_key', [
            'label' => __('URL key'),
            'class' => 'required-entry validate-identifier'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}