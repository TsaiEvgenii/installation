<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Block\Adminhtml\Form\Field\Select;

use Magento\Framework\View\Element\Html\Select;

class Stores extends Select
{
    private $storeManager;

    /**
     * Stores constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->storeManager = $storeManager;

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
            foreach ($this->getStoreFields() as $key => $label) {
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
    private function getStoreFields()
    {
        $options = [];
        foreach ($this->storeManager->getStores() as $key => $value) {
            $options[$key] = $value['name'].' - '.$value['code'];
        }

        return $options;
    }
}
