<?php
/**
 * @package Vinduesgrossisten.
 * @author Tsai Eugene <tsai.evgenii@gmail.com>
 * Copyright (c) 2025.
 */
declare(strict_types=1);

namespace BelVG\OrderUpgrader\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class MaterialImages extends AbstractFieldArray
{
    /**
     * @var MaterialRenderer
     */
    private $materialRenderer;

    /**
     * @var FileUploadRenderer
     */
    private $fileUploadRenderer;

    /**
     * @param Context $context
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $data['template'] = 'BelVG_OrderUpgrader::system/config/form/field/array.phtml';
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * Get material renderer
     *
     * @return MaterialRenderer
     * @throws LocalizedException
     */
    private function getMaterialRenderer()
    {
        if (!$this->materialRenderer) {
            $this->materialRenderer = $this->getLayout()->createBlock(
                MaterialRenderer::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->materialRenderer->setExtraParams('style="min-width: 150px; width: auto;"');
        }
        return $this->materialRenderer;
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
        $this->addColumn('material_id', [
            'label' => __('Material'),
            'renderer' => $this->getMaterialRenderer(),
            'class' => 'required-entry',
        ]);

        $this->addColumn('file', [
            'label' => __('Image'),
            'renderer' => $this->getFileUploadRenderer(),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Material Image');
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
        $materialId = $row->getData('material_id');
        if ($materialId !== null) {
            $options['option_' . $this->getMaterialRenderer()->calcOptionHash($materialId)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}