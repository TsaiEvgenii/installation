<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Total\OrderCreditmemo;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use BelVG\B2BCustomer\Model\Service\DiscountService;

/**
 * Class Discount
 *
 * @package BelVG\B2BCustomer\Model\Total\OrderCreditmemo
 */
class Discount extends AbstractTotal
{
    /**
     * @var DiscountService
     */
    protected DiscountService $discountService;

    /**
     * Discount constructor.
     *
     * @param DiscountService $discountService
     * @param string[] $data
     */
    public function __construct(
        DiscountService $discountService,
        array $data = []
    ) {
        parent::__construct($data);
        $this->discountService = $discountService;
    }

    /**
     * @param CreditmemoInterface $creditmemo
     * @return $this
     */
    public function collect(CreditmemoInterface $creditmemo): self
    {
        parent::collect($creditmemo);

        if ($order = $creditmemo->getOrder()) {
            $this->discountService->subtructDiscount($order, $creditmemo);
        }

        return $this;
    }
}
