<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Model\Service\ShippingInfo\SaveHandler;


use BelVG\ShippingManager\Model\Service\ShippingInfo\SaveHandler;
use BelVG\InstallationElements\Model\ResourceModel\ShippingInfoInstallment as ShippingInfoInstallmentResource;
use BelVG\InstallationElements\Model\ShippingInfoInstallment;
use BelVG\InstallationElements\Model\ShippingInfoInstallmentFactory;
use BelVG\ShippingManager\Api\Data\ShippingInfoInterface;
use Magento\Sales\Model\Order\Shipment;
use Psr\Log\LoggerInterface;

class UpdateInstallationData
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::UpdateInstallationDataPlugin]: ';

    public function __construct(
        protected ShippingInfoInstallmentFactory $shippingInfoInstallmentFactory,
        protected ShippingInfoInstallmentResource $shippingInfoInstallmentResource,
        protected LoggerInterface $logger
    ) {
    }

    public function afterUpdateData(
        SaveHandler $source,
        ShippingInfoInterface $result,
        int $id,
        array $data = []
    ) {
        try {
            $installationFromForm = (bool)$data['installation_is_set'];
            if ($installationFromForm !== (bool)$result->getExtensionAttributes()->getInstallation()) {
                /** @var ShippingInfoInstallment $shippingInfoInstallment */
                $shippingInfoInstallment = $this->shippingInfoInstallmentFactory->create();
                $this->shippingInfoInstallmentResource->load(
                    $shippingInfoInstallment,
                    $result->getShippinginfoId(),
                    'shippinginfo_id'
                );
                $shippingInfoInstallment->setData('is_set', $installationFromForm);
                $this->shippingInfoInstallmentResource->save($shippingInfoInstallment);

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

    public function afterSaveData(
        SaveHandler $source,
        $result,
        ?ShippingInfoInterface $shippingInfo = null,
        ?Shipment $shipment = null,
        array $data = []
    ) {
        try {
            $shippingInfoId = $result->getShippinginfoId();
            if (!$shippingInfoId) {
                return $result;
            }
            $installationFromForm = (bool)($data['installation_is_set'] ?? false);
            /** @var ShippingInfoInstallment $shippingInfoInstallment */
            $shippingInfoInstallment = $this->shippingInfoInstallmentFactory->create();
            $shippingInfoInstallment->setData('is_set', $installationFromForm);
            $shippingInfoInstallment->setData('shippinginfo_id', $shippingInfoId);
            $this->shippingInfoInstallmentResource->save($shippingInfoInstallment);
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