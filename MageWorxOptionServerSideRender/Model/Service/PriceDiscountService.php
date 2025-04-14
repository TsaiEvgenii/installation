<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Spi\PriceDiscountInterface;
use BelVG\SaleCountdown\Api\Locator\GetActualRuleInterface;
use BelVG\SalesDynamicRule\Model\Service\DynamicRuleLocator;
use Magento\Framework\Exception\NoSuchEntityException;

class PriceDiscountService implements PriceDiscountInterface
{
    private GetActualRuleInterface $getActualRuleService;
    private DynamicRuleLocator $ruleLocator;

    /**
     * PriceDiscountService constructor.
     * @param \BelVG\SaleCountdown\Api\Locator\GetActualRuleInterface $getActualRuleService
     */
    public function __construct(
        GetActualRuleInterface $getActualRuleService,
        DynamicRuleLocator $ruleLocator
    ) {
        $this->getActualRuleService = $getActualRuleService;
        $this->ruleLocator = $ruleLocator;
    }

    public function modifier(float $price, $product): float
    {
        /** @var \BelVG\SaleCountdown\Model\Countdown $sale */
        $sale = $this->getActualRule();
        if ($sale !== null) {
            try {
                $percent = $this->ruleLocator->getPercentForProductByRuleId($product, (int)$sale->getRuleId());
                if (!$percent) {
                    $percent = (float)$sale->getRuleDiscountAmount();
                }

                $price = $price * (100 - $percent) / 100;
            } catch (NoSuchEntityException $e) {

            }
        }
        return $price;
    }

    private function getActualRule()
    {
        return $this->getActualRuleService->getActualRule();
    }
}
