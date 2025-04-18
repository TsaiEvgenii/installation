<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\OrderUpgrader\Model\Service\Quote;


use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class GetQuoteService
{
    private const LOG_PREFIX = '[BelVG_OrderUpgrader::GetQuoteService]: ';

    public function __construct(
        private CartRepositoryInterface $quoteRepository
    ) {
    }

    /**
     * @param $quote
     *
     * @return CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote($quote): CartInterface
    {
        if ($quote instanceof CartInterface) {
            return $quote;
        } elseif (is_int($quote)) {
            return $this->quoteRepository->get($quote);
        }

        throw new \RuntimeException(self::LOG_PREFIX . 'Unsupported type for quote ' . gettype($quote));
    }

}