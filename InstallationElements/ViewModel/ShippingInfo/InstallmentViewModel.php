<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\ViewModel\ShippingInfo;


use BelVG\InstallationElements\Model\Service\GetInstallationItemFromOrder;
use BelVG\ShippingManager\Api\Data\ShippingInfoInterface;
use BelVG\ShippingManager\Api\Data\ShippingInfoInterfaceFactory;
use BelVG\ShippingManager\Api\ShippingInfoRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class InstallmentViewModel  implements ArgumentInterface
{
    public function __construct(
        protected ShippingInfoRepositoryInterface $shippingInfoRepository,
        protected ShippingInfoInterfaceFactory $shippingInfoFactory,
        protected GetInstallationItemFromOrder $getInstallationItemFromOrder,
        protected OrderRepositoryInterface $orderRepository,
        protected RequestInterface $request,
        protected LoggerInterface $logger
    ){
    }
    public function isInstallmentSet(): bool
    {
        $shippingInfo = $this->getShippingInfo();
        if ($shippingInfo->getShippinginfoId()) {
            return (bool)$shippingInfo->getExtensionAttributes()->getInstallation();
        }

        return false;
    }
    public function getInitValue(): string
    {
        $orderId = $this->request->getParam('order_id');
        if ($orderId === null) {
            return $this->isInstallmentSet() === false ? '0' : '1';
        }
        $order = $this->orderRepository->get($orderId);
        $installationItem = $this->getInstallationItemFromOrder->get($order);

        return $installationItem === null ? '0' : '1';
    }

    public function getShippingInfo(): ShippingInfoInterface
    {
        try {
            $shippingInfo = $this->shippingInfoRepository->getById($this->request->getParam('shippinginfo_id'));
        } catch (NoSuchEntityException|LocalizedException $e) {
            $shippingInfo = $this->shippingInfoFactory->create();
        }

        return $shippingInfo;
    }
}