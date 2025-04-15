<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Api\Data;

/**
 * Interface DeliveryRulesHandlersInterface
 *
 * @package BelVG\Factory\Api\Data
 */
interface DeliveryRulesHandlersInterface
{
    /**
     * @param $product
     * @param DeliveryRuleInterface $deliveryRule
     * @return DeliveryRuleInterface|null
     */
    public function processDeliveryRules($product, DeliveryRuleInterface $deliveryRule): ?DeliveryRuleInterface;

    /**
     * @param DeliveryRuleInterface $rule
     * @param $item
     * @param $quote
     * @return bool
     */
    public function isMatchingRule(DeliveryRuleInterface $rule, $item, $quote): bool;
}
