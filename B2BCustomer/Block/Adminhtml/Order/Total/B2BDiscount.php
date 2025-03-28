<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Adminhtml\Order\Total;


class B2BDiscount extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function initTotals()
    {
        $order = $this->getSource();
        if ($order->getData('b2b_discount') != 0) {
            $discount = new \Magento\Framework\DataObject([
                'code' => 'b2b_discount',
                'strong' => false,
                'label' => __('B2B discount (%1%)', (int)$order->getData('b2b_discount_percent')),
                'value' => '-' . $order->getData('b2b_discount')
            ]);

            if ($this->getBeforeCondition()) {
                $this->getParentBlock()->addTotalBefore($discount, $this->getBeforeCondition());
            } else {
                $this->getParentBlock()->addTotal($discount, $this->getAfterCondition());
            }
        }

        return $this;
    }
}
