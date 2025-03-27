<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Block\Adminhtml\Form\Field;


use Magento\Framework\View\Element\Template;

class Textarea extends Template
{
    public function _toHtml(): string
    {
        $inputName = $this->getInputName();
        $column = $this->getColumn();

        return '<textarea id="' . $this->getInputId() . '" name="' . $inputName . '" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            ($column['class'] ?? 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '></textarea>';
    }
}