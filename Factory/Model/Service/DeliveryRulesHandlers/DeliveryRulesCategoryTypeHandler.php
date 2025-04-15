<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Service\DeliveryRulesHandlers;

use BelVG\Factory\Api\Data\DeliveryRuleInterface;
use BelVG\Factory\Api\Data\DeliveryRulesHandlersInterface;
use BelVG\OrderFactory\Model\QuoteItemData;

/**
 * Class DeliveryRulesCategoryTypeHandler
 *
 * @package BelVG\Factory\Model\Service\DeliveryRulesHandlers
 */
class DeliveryRulesCategoryTypeHandler implements DeliveryRulesHandlersInterface
{
    /**
     * @var QuoteItemData
     */
    protected QuoteItemData $quoteItemData;

    /**
     * DeliveryRulesCategoryTypeHandler constructor.
     *
     * @param QuoteItemData $quoteItemData
     */
    public function __construct(QuoteItemData $quoteItemData)
    {
        $this->quoteItemData = $quoteItemData;
    }

    /**
     * @inheritdoc
     */
    public function processDeliveryRules($product, DeliveryRuleInterface $deliveryRule): ?DeliveryRuleInterface
    {
        $productCategoryIds = $product->getCategoryIds();
        if ((int) $deliveryRule->getCategoryId() === 0 || in_array($deliveryRule->getCategoryId(), $productCategoryIds)) {
            return $deliveryRule;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isMatchingRule(DeliveryRuleInterface $rule, $item, $quote): bool
    {
        // [category_id1 => true, category_id2 => true, ...]
        $categoryIdMap = array_fill_keys(
            $this->quoteItemData->getCategoryIds($item),
            true);
        $colors = $this->quoteItemData->getColors($item, $quote);

        return (!$rule->getColors() || $rule->getColors() == $colors)
            && (!$rule->getCategoryId() || isset($categoryIdMap[$rule->getCategoryId()]));
    }
}
