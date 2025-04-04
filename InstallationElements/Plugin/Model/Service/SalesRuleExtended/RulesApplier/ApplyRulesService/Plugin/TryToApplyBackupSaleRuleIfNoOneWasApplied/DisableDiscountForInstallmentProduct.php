<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Model\Service\SalesRuleExtended\RulesApplier\ApplyRulesService\Plugin\TryToApplyBackupSaleRuleIfNoOneWasApplied;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use BelVG\OrderEdit\Model\Service\SalesRuleExtended\RulesApplier\ApplyRulesService\Plugin\TryToApplyBackupSaleRuleIfNoOneWasApplied;
use Psr\Log\LoggerInterface;

class DisableDiscountForInstallmentProduct
{    private const LOG_PREFIX = '[BelVG_MeasurementRequest::DisableDiscountForMeasurementProductPlugin]: ';

    public function __construct(
        private readonly InstallationProductConfig $installationProductConfig,
        private readonly LoggerInterface $logger
    ) {
    }

    public function afterCanRun(
        TryToApplyBackupSaleRuleIfNoOneWasApplied $source,
        $result,
        $item,
        $rules,
        $skipValidation,
        $couponCode,
        $appliedRuleIds
    ): bool {
        try {
            if ($item && $item->getSku() == $this->installationProductConfig->getProductSku()) {
                return false;
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return $result;
    }

}