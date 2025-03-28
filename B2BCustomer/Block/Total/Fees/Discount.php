<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Block\Total\Fees;

use BelVG\QuotePdf\API\Data\Block\Total\FeeInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;

class Discount extends \Magento\Framework\View\Element\Template implements FeeInterface
{
    protected PriceCurrencyInterface $priceCurrency;

    /** @var \Magento\Quote\Model\Quote $quote */
    protected $quote = null;

    protected $_template = 'pdf/total/fees/discount.phtml';

    protected $discountPercent;

    private array $params = [];

    public function __construct(
        TemplateContext        $context,
        PriceCurrencyInterface $priceCurrency,
        array                  $data = []
    )
    {
        $this->priceCurrency = $priceCurrency;

        parent::__construct($context, $data);
    }

    /**
     * @param $quote
     * @param array $params
     * @return string
     */
    public function renderFee(
        $quote,
        array $params = []
    ): string
    {
        $this->quote = $quote;
        $this->params = $params;

        return $this->toHtml();
    }

    /**
     * @param $price
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function formatPrice($price): string
    {
        $this->checkQuote();

        return $this->priceCurrency->format(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->quote->getStore()
        );
    }

    /**
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTotal(): float
    {
        $this->checkQuote();

        if ($this->quote instanceof \Magento\Sales\Model\Order) {
            $total = $this->quote->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY);
            $this->discountPercent = $this->quote->getData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY);
        } else {
            if ($this->quote->getData('is_virtual')) {
                $totalsModel = $this->quote->getBillingAddress()->getTotals();
            } else {
                $totalsModel = $this->quote->getShippingAddress()->getTotals();
            }

            $this->discountPercent = $this->quote->getData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY);
            $total = $totalsModel[B2BDiscountTotals::B2B_DISCOUNT_KEY] ?? 0;
            if ($total instanceof \Magento\Quote\Model\Quote\Address\Total) {
                $total = $total->getValue();
            }
        }

        $this->handleAdditionalParams($total);

        return -(float)$total;
    }

    public function getTotalPercent(): int
    {
        return (int)$this->discountPercent;
    }

    /**
     * @param $total
     */
    private function handleAdditionalParams(&$total)
    {
        if (isset($this->params['isVatExcluded']) && $this->params['isVatExcluded']) {
            $total /= $this->params['tax_rate'];
        }
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkQuote(): void
    {
        if ($this->quote === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Quote is not set'));
        }
    }
}
