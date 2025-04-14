<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Block\Adminhtml\Form\Field\Select;

use Magento\Framework\View\Element\Html\Select;

class TaxRates extends Select
{
    private $taxRatesCollectionFactory;

    public function __construct(
        \Magento\Tax\Model\TaxRateCollectionFactory $taxRatesCollectionFactory,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->taxRateCollectionFactory = $taxRatesCollectionFactory;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->getTaxRulesFields() as $key => $label) {
                $this->addOption($key, $label);
            }
        }

        return parent::_toHtml();
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value){
        return $this->setName($value);
    }

    /**
     * @return array
     */
    private function getTaxRulesFields()
    {
        /** @var \Magento\Tax\Model\TaxRateCollection $collection */
        $collection = $this->taxRateCollectionFactory->create();

        $options = [];
        foreach ($collection as $key => $value) {
            $options[$value->getData('tax_calculation_rate_id')] = $value->getData('code') . ' (' . $value->getData('tax_country_id') . ', ' . $value->getData('rate'). ')';
        }

        return $options;
    }
}
