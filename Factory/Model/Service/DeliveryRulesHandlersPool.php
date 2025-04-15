<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Service;

use BelVG\Factory\Api\Data\DeliveryRulesHandlersInterface;

/**
 * Class DeliveryRulesHandlersPool
 *
 * @package BelVG\Factory\Model\Service
 */
class DeliveryRulesHandlersPool
{
    /**
     * @var DeliveryRulesHandlersInterface[]
     */
    protected array $deliveryRulesHandlers;

    /**
     * DeliveryRulesHandlersPool constructor.
     *
     * @param array $deliveryRulesHandlers
     */
    public function __construct(array $deliveryRulesHandlers = [])
    {
        $this->deliveryRulesHandlers = $deliveryRulesHandlers;
    }

    /**
     * @return array
     */
    public function getDeliveryRulesHandlersPool(): array
    {
        return $this->deliveryRulesHandlers;
    }
}
