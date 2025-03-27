<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Block\Adminhtml\Form\Field;


use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class ConstructionWasteDisposalPrice extends AbstractFieldArray
{

    protected function _prepareToRender(): void
    {
        $this->addColumn('construction_from_items', [
            'label' => __('From items'),
            'class' => 'required-entry validate-number validate-zero-or-greater'
        ]);
        $this->addColumn('construction_to_items', [
            'label' => __('To items'),
            'class' => 'required-entry validate-number validate-zero-or-greater'
        ]);
        $this->addColumn('construction_price', [
            'label' => __('Price'),
            'class' => 'required-entry validate-number validate-zero-or-greater'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

}