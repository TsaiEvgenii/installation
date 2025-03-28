<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\B2BCustomer\Plugin\Model\Service\PriceDiscountService;


use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;
use BelVG\InfinityLoopQuoteFix\Model\Service\GetQuote;
use Magento\Customer\Model\Session as CustomerSession;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Http\Context as HttpContext;

class AddB2bDiscount
{
    private const LOG_PREFIX = '[BelVG_B2BCustomer::AddB2bDiscountPlugin]: ';

    public function __construct(
        protected CustomerRepositoryInterface $customerRepository,
        protected HttpContext $httpContext,
        protected CustomerSession $customerSession,
        protected GetQuote $getQuoteService,
        protected Config $config,
        protected LoggerInterface $logger
    ) {
    }

    public function afterModifier(
        $source,
        $priceWithDiscount,
        float $price,
        $product
    ): float {
        try {
            $b2bDiscountValue = $this->httpContext->getValue(B2BDiscountTotals::B2B_DISCOUNT_KEY);
            if ($b2bDiscountValue) {
                $maxDiscount = $this->config->getDiscountMaxValueCart();
                $currentQuote = $this->getQuoteService->getCurrentQuote();
                //If current quote exists
                if ($currentQuote->getId() && (int)$currentQuote->getItemsQty() > 0) {
                    $b2bDiscountPercent = $currentQuote->getData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY);
                    $b2bDiscountAmount = $this->getB2bDiscountAmount($price, $b2bDiscountPercent);
                } else {
                    $currentDiscount = $this->getCurrentDiscountValue($price, $priceWithDiscount);
                    if ($currentDiscount >= $maxDiscount) {
                        return $priceWithDiscount;
                    }

                    if ($maxDiscount - $currentDiscount - $b2bDiscountValue < 0) {
                        $calculatedB2bDiscountValue = $maxDiscount + $currentDiscount;
                        $b2bDiscountAmount = $this->getB2bDiscountAmount($price, $calculatedB2bDiscountValue);
                    } else {
                        $b2bDiscountAmount = $this->getB2bDiscountAmount($price, $b2bDiscountValue);
                    }
                }

                return $priceWithDiscount - $b2bDiscountAmount;
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }


        return $priceWithDiscount;
    }

    protected function getCurrentDiscountValue($price, $priceWithDiscount): float
    {
        $discount = $price - $priceWithDiscount;
        $discountPercent = round($discount * 100 / $price, 2);
        return $discountPercent;
    }

    protected function getB2bDiscountAmount($price, $discountPercent): float|int
    {
        return $price - ($price * (100 - $discountPercent) / 100);
    }
}