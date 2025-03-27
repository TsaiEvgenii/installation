<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Model\ResourceModel\ShippingInfo\Grid\Collection;


use BelVG\ShippingManager\Model\ResourceModel\ShippingInfo\Grid\Collection;
use Psr\Log\LoggerInterface;

class SetInstallationDataForGrid
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::SetInstallationDataForGridPlugin]: ';

    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function afterPrepareCollectionQuery(
        Collection $source,
        $result
    ) {
        try {
            $source->getSelect()
                ->joinLeft(
                    [
                        'shipping_info_installment' => $source->getResource()
                            ->getTable('belvg_shippingmanager_shippinginfo_installment')
                    ],
                    'shipping_info_installment.shippinginfo_id = main_table.shippinginfo_id',
                    [
                        'shipping_info_installment.is_set as installment',
                    ]
                );

            $source->addFilterToMap('installment', 'shipping_info_installment.is_set');
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