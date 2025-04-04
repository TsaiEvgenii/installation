<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Block\Adminhtml\Form\Field;


use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class DoorTypePrice extends AbstractFieldArray
{
    private ?Textarea $textAreaRenderer = null;
    protected function _prepareToRender(): void
    {
        $this->addColumn('door_type_sku_prefix', [
            'label' => __('SKU Prefix'),
            'class' => 'required-entry',
            'renderer' => $this->getTextAreaRenderer()
        ]);
        $this->addColumn('door_type_price', [
            'label' => __('Price'),
            'class' => 'required-entry validate-number validate-zero-or-greater'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
    private function getTextAreaRenderer(): ?Textarea
    {
        if (!$this->textAreaRenderer) {
            $this->textAreaRenderer = $this->getLayout()->createBlock(
                Textarea::class,
                ''
            );
        }
        return $this->textAreaRenderer;
    }

}