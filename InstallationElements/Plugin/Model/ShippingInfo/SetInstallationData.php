<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Model\ShippingInfo;

use BelVG\InstallationElements\Model\ResourceModel\ShippingInfoInstallment as ShippingInfoInstallmentResource;
use BelVG\InstallationElements\Model\ShippingInfoInstallment;
use BelVG\InstallationElements\Model\ShippingInfoInstallmentFactory;
use BelVG\ShippingManager\Api\Data\ShippingInfoInterface;
use Psr\Log\LoggerInterface;

class SetInstallationData
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::SetInstallationDataPlugin]: ';
    public function __construct(
        protected ShippingInfoInstallmentFactory $shippingInfoInstallmentFactory,
        protected ShippingInfoInstallmentResource $shippingInfoInstallmentResource,
        protected LoggerInterface $logger
    ){}

    public function afterGetDataModel(
        \BelVG\ShippingManager\Model\ShippingInfo $source,
        ShippingInfoInterface $result
    ){
        try {
            if(!($shippingFileId = $result->getShippinginfoId())){
                return $result;
            }
            /** @var ShippingInfoInstallment $shippingInfoInstallment */
            $shippingInfoInstallment = $this->shippingInfoInstallmentFactory->create();
            $this->shippingInfoInstallmentResource->load($shippingInfoInstallment, $shippingFileId, 'shippinginfo_id');
            if ($shippingInfoInstallment->getId()) {
                $result->getExtensionAttributes()->setInstallation($shippingInfoInstallment->getData('is_set'));
            } else {
                $result->getExtensionAttributes()->setInstallation(false);
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