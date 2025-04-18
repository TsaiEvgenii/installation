<?php
/**
 * @package Vinduesgrossisten.
 * @author Tsai Eugene
 * @copyright (c) 2025
 */
declare(strict_types=1);

namespace BelVG\OrderUpgrader\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class FileUploadRenderer extends Template implements RendererInterface
{
    /**
     * Render method called by Magento to output the custom column field
     */
    public function render(AbstractElement $element): string
    {
        $this->setInputName($element->getName());
        $this->setInputId($element->getHtmlId());
        return $this->_toHtml();
    }

    /**
     * Set input name
     */
    public function setInputName($value): self
    {
        return $this->setData('input_name', $value);
    }

    /**
     * Set input ID
     */
    public function setInputId($value): self
    {
        return $this->setData('input_id', $value);
    }

    public function getInputName(): string
    {
        return (string) $this->getData('input_name');
    }

    public function getInputId(): string
    {
        return (string) $this->getData('input_id');
    }

    /**
     * Render file input and image preview (if available)
     */
    public function _toHtml(): string
    {
        $inputId = $this->getInputId();
        $inputName = $this->getInputName();

        // File input field
        $html = '<input type="file" id="' . $inputId . '" name="' . $inputName . '" class="input-file" />';

        // Safe hidden field output with conditional existence of "file"
        $html .= '<% if (typeof file !== "undefined") { %>';
        $html .= '<input type="hidden" id="' . $inputId . '_value" name="' . $inputName . '" value="<%- file %>" />';
        $html .= '<% } else { %>';
        $html .= '<input type="hidden" id="' . $inputId . '_value" name="' . $inputName . '" value="" />';
        $html .= '<% } %>';


        // Image preview (only if file is defined)
        $html .= '<% if (typeof file !== "undefined" && file) { %>';
        $html .= '<br/><img src="' . $this->getBaseUrl() . 'media/<%- file %>" alt="preview" height="50" />';
        $html .= '<p class="note"><span><%- file %></span></p>';
        $html .= '<% } %>';

        return $html;
    }
}