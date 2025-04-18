<?php
/**
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);

namespace BelVG\OrderUpgrader\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class OptionValues extends AbstractFieldArray
{
    /**
     * @var OptionTypeRenderer
     */
    private $optionTypeRenderer;

    /**
     * @var FileUploadRenderer
     */
    private $fileUploadRenderer;

    public function __construct(
        Context $context,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $data['template'] = 'BelVG_OrderUpgrader::system/config/form/field/array.phtml';
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * Get option type renderer
     *
     * @return OptionTypeRenderer
     * @throws LocalizedException
     */
    private function getOptionTypeRenderer()
    {
        if (!$this->optionTypeRenderer) {
            $this->optionTypeRenderer = $this->getLayout()->createBlock(
                OptionTypeRenderer::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->optionTypeRenderer->setExtraParams('style="min-width: 70px; width: auto;"');

        }
        return $this->optionTypeRenderer;
    }

    /**
     * Get file upload renderer
     *
     * @return FileUploadRenderer
     * @throws LocalizedException
     */
    private function getFileUploadRenderer(): FileUploadRenderer
    {
        if (!$this->fileUploadRenderer) {
            $this->fileUploadRenderer = $this->getLayout()->createBlock(
                FileUploadRenderer::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->fileUploadRenderer;
    }

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('option_code', [
            'label' => __('Option Type'),
            'renderer' => $this->getOptionTypeRenderer(),
            'class' => 'required-entry',
        ]);

        $this->addColumn('label', [
            'label' => __('Display Label'),
            'class' => 'required-entry',
            'style' => 'min-width: 80px; white-space: nowrap;',
        ]);

        $this->addColumn('value', [
            'label' => __('System Value'),
            'class' => 'required-entry',
            'style' => 'min-width: 80px; white-space: nowrap;',
        ]);

        $this->addColumn('file', [
            'label' => __('Image'),
            'renderer' => $this->getFileUploadRenderer(),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Option Value');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $optionCode = $row->getData('option_code');
        if ($optionCode !== null) {
            $options['option_' . $this->getOptionTypeRenderer()->calcOptionHash($optionCode)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}