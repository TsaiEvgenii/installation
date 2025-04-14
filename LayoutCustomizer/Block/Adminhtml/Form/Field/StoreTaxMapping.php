<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class StoreTaxMapping extends AbstractFieldArray
{
    /** @var \BelVG\LayoutCustomizer\Block\Adminhtml\Form\Field\Select\Stores */
    private $storeFieldRenderer;

    /** @var \BelVG\LayoutCustomizer\Block\Adminhtml\Form\Field\Select\TaxRates */
    private $taxRateFieldRenderer;

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('store', array(
            'label' => __('Store'),
            'renderer' => $this->getStoreFieldRenderer(),
        ));
        $this->addColumn('tax_rate', array(
            'label' => __('Tax Rate'),
            'renderer' => $this->getTaxRateFieldRenderer(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add new mapping');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $row->setData(
            'option_extra_attrs',
            [
                'option_' . $this->getStoreFieldRenderer()->calcOptionHash($row->getData('store')) => 'selected="selected"',
                'option_' . $this->getTaxRateFieldRenderer()->calcOptionHash($row->getData('tax_rate')) => 'selected="selected"'
            ]
        );
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStoreFieldRenderer()
    {
        if (!$this->storeFieldRenderer) {
            $this->storeFieldRenderer = $this->getLayout()->createBlock(
                \BelVG\LayoutCustomizer\Block\Adminhtml\Form\Field\Select\Stores::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->storeFieldRenderer->setClass('shipping_type_select');
        }

        return $this->storeFieldRenderer;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTaxRateFieldRenderer()
    {
        if (!$this->taxRateFieldRenderer) {
            $this->taxRateFieldRenderer = $this->getLayout()->createBlock(
                \BelVG\LayoutCustomizer\Block\Adminhtml\Form\Field\Select\TaxRates::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->taxRateFieldRenderer->setClass('shipping_type_select');
        }

        return $this->taxRateFieldRenderer;
    }
}
