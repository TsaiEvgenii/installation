<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\InstallationProductHandler;


use BelVG\InstallationElements\Api\Service\InstallationProductHandler\HandlerInterface;
use BelVG\InstallationElements\Model\Service\CheckOrderInstallation;
use BelVG\InstallationElements\Model\Service\Config;
use BelVG\InstallationElements\Model\Service\GetInstallationItemFromOrder;
use BelVG\InstallationElements\Model\Service\GetOrderQty;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SendEmailHandler implements HandlerInterface
{
    private const CONTENT_SEPARATOR = '----------';
    private const LOG_PREFIX = '[BelVG_InstallationElements::SendEmailHandler]: ';


    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected Config $installationElementsConfig,
        protected CheckOrderInstallation $checkOrderInstallationService,
        protected GetOrderQty $getOrderQtyService,
        protected GetInstallationItemFromOrder $getInstallationItemFromOrderService,
        protected WebsiteRepositoryInterface $websiteRepository,
        protected TransportBuilder $transportBuilder,
        protected PriceHelper $priceHelper,
        protected PriceCurrencyInterface $priceCurrency,
        protected Emulation $emulation,
        protected LoggerInterface $logger
    ){

    }

    /**
     * @throws NoSuchEntityException
     */
    public function isAvailable(OrderInterface $order): bool
    {
        $subscribersList = $this->getInternalSubscribers($order);
        if (count($subscribersList) === 0) {
            return false;
        }

        return $this->checkOrderInstallationService->orderIncludeInstallationProduct((int)$order->getId());
    }

    public function execute(OrderInterface $order): void
    {
        try {
            $storeId = $order->getStoreId();
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->getNewRequestEmailTemplate($order->getStoreId()))
                ->setTemplateOptions($this->getTemplateOptions())
                ->setTemplateVars($this->getVariables($order))
                ->setFromByScope('general')
                ->addTo($this->getInternalSubscribers($order))
                ->getTransport();
            $transport->sendMessage();
            $this->emulation->stopEnvironmentEmulation();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));

        }
    }

    protected function getNewRequestEmailTemplate($storeId): string
    {
        $routePlannerSettings = $this->installationElementsConfig->getRouteplannerSettings((int)$storeId);

        return $routePlannerSettings['request_email_tpl'] ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getTemplateOptions(): array
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        ];
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getVariables(OrderInterface $order) {
        $content[(string)__('Firstname')] = $order->getCustomerFirstname();
        $content[(string)__('Lastname')] = $order->getCustomerLastname();
        $content[(string)__('Email')] = $order->getCustomerEmail();
        /** @var \Magento\Sales\Model\Order\Address|null $shippingAddress */
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $content[(string)__('Phone')] = $shippingAddress->getTelephone();
            $content[(string)__('Address')] = implode(' ', $shippingAddress->getStreet());
            $content[(string)__('Postcode')] = $shippingAddress->getPostcode();
            $content[(string)__('City')] = $shippingAddress->getCity();
        }
        $content[self::CONTENT_SEPARATOR] = self::CONTENT_SEPARATOR;
        $content[(string)__('Qty')] = $this->getOrderQtyService->get($order);
        $installationItem = $this->getInstallationItemFromOrderService->get($order);

        $additionalOptions = $installationItem->getProductOptionByCode('additional_options') ?? [];
        foreach ($additionalOptions as $additionalOption) {
            $hidden = $additionalOption['hidden'] ?? true;
            $formattedPrice = $additionalOption['formatted_price'] ?? '';
            if ($hidden || $formattedPrice === '') {
                continue;
            }
            $content[(string)__($additionalOption['label'] ?? '')] = $formattedPrice;
        }

        $content[(string)(__('Total'))] = $this->priceCurrency->format(
            $installationItem->getRowTotalInclTax() ?? 0,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $order->getStore()
        );


        return [
            'messageBody' => $this->prepareContent($content),
            'customerName' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            'subject' => $this->getSubject($order)
        ];
    }

    private function prepareContent(iterable $content): string
    {
        $newContent = [];
        foreach ($content as $key => $value) {
            $pattern = '<b>%s</b> = %s';
            if ($key === self::CONTENT_SEPARATOR) {
                $pattern = '<b>%s</b>';
            }

            $newContent[] = sprintf(
                $pattern,
                $key,
                $value
            );
        }

        return implode('<br>', $newContent);
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getSubject(OrderInterface $order): string
    {
        $websiteId = (int)$this->storeManager->getStore($order->getStoreId())->getWebsiteId();
        $website = $this->websiteRepository->getById($websiteId);
        return (string)__('New installation order #%orderIncrementId from %websiteName', [
            'websiteName' => $website->getName(),
            'orderIncrementId' => $order->getIncrementId()
        ]);
    }

    private function getInternalSubscribers($order): array
    {
        return explode(
            ',',
            $this->installationElementsConfig->getSubscribersList((int)$order->getStoreId()) ?? ''
        );
    }

}