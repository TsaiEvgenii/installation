<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\Quote;


use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class GetQuote
{

    private const LOG_PREFIX = '[BelVG_MadeInDenmark::GetQuoteService]: ';

    public function __construct(
        protected CartRepositoryInterface $quoteRepository,
        protected CheckoutSession $checkoutSession
    ) {
    }

    public function getQuote($quote) :CartInterface {
        if ($quote instanceof CartInterface) {
            return $quote;
        } elseif (is_int($quote)) {
            return $this->quoteRepository->get($quote);
        }

        throw new \RuntimeException(self::LOG_PREFIX . 'Unsupported type for quote ' . gettype($quote));
    }
}