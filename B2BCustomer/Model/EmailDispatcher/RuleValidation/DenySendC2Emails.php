<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\EmailDispatcher\RuleValidation;

use BelVG\EmailDispatcher\Api\Data\EmailTypeInterface;
use BelVG\EmailDispatcher\Api\EmailTemplateResolverInterface;
use BelVG\EmailDispatcher\Api\RuleValidationResultInterface;
use BelVG\EmailDispatcher\Model\Data\RuleValidationResultFactory;
use BelVG\OrderEdit\Model\Config;
use BelVG\OrderEdit\Model\Service\IsAllowed\Conditions\IsAllowedToEditOrder;
use Magento\Sales\Model\Order;
use BelVG\OrderEdit\Model\EmailDispatcher\RuleValidation\AbstractRule;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template as TemplateContainer;
use BelVG\B2BCustomer\Model\Config as B2BConfig;
use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;

class DenySendC2Emails extends AbstractRule
{

    const MESSAGE = 'C2 can\'t be send to b2b customers with split payment';
    /**
     * @var IsB2BSplitService
     */
    protected IsB2BSplitService $b2BSplitService;

    /**
     * @var B2BConfig
     */
    protected B2BConfig $b2bConfig;

    /**
     * @param RuleValidationResultFactory $ruleValidationResultFactory
     * @param EmailTemplateResolverInterface $emailTemplateResolver
     * @param IsB2BSplitService $b2BSplitService
     * @param IsAllowedToEditOrder $isAllowedToEditOrder
     * @param B2BConfig $b2bConfig
     * @param Config $config
     */
    public function __construct(
        RuleValidationResultFactory    $ruleValidationResultFactory,
        EmailTemplateResolverInterface $emailTemplateResolver,
        IsB2BSplitService              $b2BSplitService,
        IsAllowedToEditOrder           $isAllowedToEditOrder,
        B2BConfig                      $b2bConfig,

        Config                         $config
    )
    {
        $this->b2BSplitService = $b2BSplitService;
        $this->b2bConfig = $b2bConfig;
        parent::__construct($ruleValidationResultFactory, $emailTemplateResolver, $isAllowedToEditOrder, $config);
    }


    /**
     * @param Order $order
     * @param TemplateContainer $templateContainer
     * @param IdentityInterface $identity
     * @return RuleValidationResultInterface
     */
    public function isApplicable(Order $order, TemplateContainer $templateContainer, IdentityInterface $identity): RuleValidationResultInterface
    {
        $result = $this->ruleValidationResultFactory->create(
            [
                'data' => [RuleValidationResultInterface::IS_APPLICABLE => true,
                    RuleValidationResultInterface::MESSAGE => self::MESSAGE]]
        );
        if ($this->b2BSplitService->isAllowed($order->getCustomerGroupId(), $order->getStoreId())) {
            $disabledStatuses = $this->b2bConfig->getB2BSplitDisableStatusEmailSend((int)$order->getStoreId());
            if ($disabledStatuses) {
                $disabledStatuses = explode(",", $disabledStatuses);
                foreach ($disabledStatuses as $disabledStatus) {
                    if ($order->getStatus() === $disabledStatus) {
                        $result->setData(RuleValidationResultInterface::IS_APPLICABLE, false);
                    }
                }
            }
        }
        return $result;
    }
}
