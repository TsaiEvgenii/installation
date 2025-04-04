<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Block\Adminhtml\Form\Field;


use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class DoorSquarePrice extends AbstractFieldArray
{
    protected ?Textarea $textAreaRenderer = null;
    protected function _prepareToRender(): void
    {

        $this->addColumn('door_sqr_sku_prefix', [
            'label' => __('SKU Prefix'),
            'class' => 'required-entry',
            'renderer' => $this->getTextAreaRenderer()
        ]);
        $this->addColumn('door_sqr_from_sqr', [
            'label' => __('From sqr'),
            'class' => 'required-entry validate-number validate-zero-or-greater'
        ]);
        $this->addColumn('door_sqr_to_sqr', [
            'label' => __('To sqr'),
            'class' => 'required-entry validate-number validate-zero-or-greater'
        ]);
        $this->addColumn('door_sqr_price', [
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