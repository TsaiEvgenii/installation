<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\Service;


use BelVG\AdditionalServices\Model\Service\GetSubtotalWithoutServices;
use BelVG\B2BCustomer\Model\Config;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class CurrentDiscountService
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var DiscountService
     */
    private $discountService;

    /**
     * @param Config $config
     */
    public function __construct(
        protected GetSubtotalWithoutServices $getSubtotalWithoutServices,
        Config $config,
        PriceCurrencyInterface $priceCurrency,
        \BelVG\B2BCustomer\Model\Service\DiscountService $discountService
    )
    {
        $this->config = $config;
        $this->discountService = $discountService;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param $total
     * @param $b2bDiscount
     * @return float|int
     */
    private function getCurrentDiscount($quote, $total, $b2bDiscount)
    {
        $discount = $this->priceCurrency->convert($b2bDiscount);
        $discount = $this->discountService->getDiscountAmount($quote, $discount);
        $discountAmount = abs($total->getDiscountAmount() - $discount);

        $subtotal = $this->getSubtotalWithoutServices->getSubtotalInclTaxFromQuote($quote);
        if ($subtotal === 0) {
            return 0;
        }

        return (int) $discountAmount / $subtotal * 100;
    }

    /**
     *
     * Check if current discount + b2b discount bigger than allowed
     * @param $total
     * @param $b2bDiscount
     * @return bool
     */
    public function isAllowed($quote, $total, $b2bDiscount)
    {
        $currentDiscount = $this->getCurrentDiscount($quote, $total, $b2bDiscount);
        $maxDiscount = $this->config->getDiscountMaxValueCart();
        if ($currentDiscount > $maxDiscount) {
            return true;
        }
        return false;
    }
}
