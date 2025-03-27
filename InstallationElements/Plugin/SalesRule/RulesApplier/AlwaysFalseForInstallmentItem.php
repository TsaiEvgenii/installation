<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\SalesRule\RulesApplier;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use Magento\Framework\Stdlib\DateTime;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as SalesRuleCollectionFactory;
use Magento\SalesRule\Model\RulesApplier as Subject;
use Psr\Log\LoggerInterface;

class AlwaysFalseForInstallmentItem
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::AlwaysFalseForInstallmentItem]: ';

    public function __construct(
        private readonly InstallationProductConfig $installationProductConfig,
        private readonly DateTime $dateTime,
        private readonly SalesRuleCollectionFactory $salesRuleCollectionFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param Subject $subject
     * @param QuoteItem $item
     * @param $rules
     * @param $skipValidation
     * @param $couponCode
     * @return array|null
     */
    public function beforeApplyRules(
        Subject $subject,
        QuoteItem $item,
        $rules,
        $skipValidation,
        $couponCode
    ): ?array {
        try {
            if ($this->isInstallmentService($item)) {
                $unrealTime = $this->dateTime->formatDate(strtotime("+1 month"));
                $rulesCollection = $this->salesRuleCollectionFactory
                    ->create()
                    ->setFlag('validation_filter', true)
                    ->addFieldToFilter('from_date', $unrealTime)
                    ->load();
                $rules = $rulesCollection->getItems();

                return [$item, $rules, $skipValidation, $couponCode];
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return null;
    }

    private function isInstallmentService(QuoteItem $item) :bool {
        return $item->getSku() &&
            $item->getSku() == $this->installationProductConfig->getProductSku();
    }
}