<?php
namespace BelVG\B2BCustomer\Model\Total\Quote;


use BelVG\AdditionalServices\Model\Service\GetSubtotalWithoutServices;
use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\CurrentDiscountService;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class B2BDiscountTotals extends AbstractTotal
{

    const B2B_DISCOUNT_KEY = 'b2b_discount';

    const B2B_DISCOUNT_PERCENT_KEY = 'b2b_discount_percent';

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \BelVG\B2BCustomer\Model\Service\DiscountService
     */
    protected $discountService;

    /**
     * @var CustomerCheck
     */
    protected $customerCheck;

    protected $config;

    protected $currentDiscountService;

    /**
     * @param \BelVG\B2BCustomer\Model\Service\DiscountService $discountService
     * @param CustomerCheck $customerCheck
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \BelVG\B2BCustomer\Model\Service\DiscountService $discountService,
        CustomerCheck $customerCheck,
        Config $config,
        CurrentDiscountService $currentDiscountService,
        protected GetSubtotalWithoutServices $getSubtotalWithoutServices,
        PriceCurrencyInterface $priceCurrency
    )
    {
        $this->setCode(self::B2B_DISCOUNT_KEY);
        $this->customerCheck = $customerCheck;
        $this->currentDiscountService = $currentDiscountService;
        $this->config = $config;
        $this->discountService = $discountService;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this|B2BDiscountTotals
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if (count($shippingAssignment->getItems()) !== 0 && $total->getGrandTotal()) {

            parent::collect($quote, $shippingAssignment, $total);
            $discountValue = $quote->getCustomer()->getCustomAttribute(self::B2B_DISCOUNT_KEY)?->getValue();
            if ($discountValue && $this->isAllowed($quote) && $quote->getCustomer()->getId()) {

                $maxDiscount = $this->config->getDiscountMaxValueCart();
                $discount = $this->priceCurrency->convert($discountValue);
                $discount = $this->discountService->getDiscountAmount($quote, $discount);

                $amountWithDiscount = $total->getGrandTotal() - $discount;
                $baseAmountWithDiscount = $total->getBaseGrandTotal() - $discount;

                if ($this->currentDiscountService->isAllowed($quote, $total, $discountValue)) {
                    $subtotal = $this->getSubtotalWithoutServices->getSubtotalInclTaxFromQuote($quote);
                    $discountValue = $this->priceCurrency->convert(round($maxDiscount - ((abs($total->getDiscountAmount()) / $subtotal) * 100)));
                    if ($discountValue < 0) {
                        $quote->setData($this->getCode(), 0);
                        return $this;
                    }
                    $discount = abs($this->discountService->getDiscountAmount($quote, $discountValue));
                    $amountWithDiscount = $total->getGrandTotal() - $discount;
                    $baseAmountWithDiscount = $total->getBaseGrandTotal() - $discount;
                }

                $taxAmount = $this->discountService->getTaxAmount($total, $amountWithDiscount);
                $baseTaxAmount = $this->discountService->getTaxAmount($total, $baseAmountWithDiscount);

                $total->addTotalAmount($this->getCode(), -$discount);
                $total->addBaseTotalAmount($this->getCode(), -$discount);
                $total->setData($this->getCode(), $discount);

                $total->setTaxAmount($taxAmount);
                $total->setBaseTaxAmount($baseTaxAmount);
                $total->setGrandTotal($amountWithDiscount);
                $total->setBaseGrandTotal($baseAmountWithDiscount);

                $quote->setData($this->getCode(), $discount);
                $quote->setData(self::B2B_DISCOUNT_PERCENT_KEY, $discountValue );
            } else {

                $quote->setData($this->getCode(), 0);
                $quote->setData(self::B2B_DISCOUNT_PERCENT_KEY, 0);
            }
        }
        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ): array {
        if ($quote->getCustomer()->getId()
            && $quote->getCustomer()->getCustomAttribute(self::B2B_DISCOUNT_KEY)
            && $quote->getData($this->getCode())) {

            $quote->setData('subtotal_incl_tax', $total->getData('subtotal_incl_tax'));
            return [
                'code' => $this->getCode(),
                'title' => __('B2B discount <b>(-%1%)</b>', (int)$quote->getData(self::B2B_DISCOUNT_PERCENT_KEY)),
                'value' => $quote->getData($this->getCode())
            ];
        }
        return [];
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return __('B2B discount');
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    public function isAllowed(Quote $quote): bool
    {
        $groupId = $quote->getCustomer()->getGroupId();
        return $groupId && $this->customerCheck->isB2BCustomer($groupId, $quote->getStoreId());
    }
}
