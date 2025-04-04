<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Api\Data\InstallationInterface;
use BelVG\InstallationElements\Api\Webapi\AddInstallationProductInterface;
use BelVG\InstallationElements\Model\Service\Quote\AddInstallationProductToQuote;
use BelVG\InstallationElements\Model\Service\Quote\GetQuote;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResource;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class AddInstallationProduct implements AddInstallationProductInterface
{
    public function __construct(
        private readonly Emulation $emulation,
        private QuoteIdMaskFactory $quoteIdMaskFactory,
        private QuoteIdMaskResource $quoteIdMaskResource,
        private GetQuote $getQuoteService,
        private AddInstallationProductToQuote $addInstallationProductToQuoteService,
        private MessageManagerInterface $messageManager
    ){

    }

    public function addProduct(string $cartId, string $storeId, InstallationInterface $installationData): void
    {
        $this->emulation->startEnvironmentEmulation((int)$storeId, Area::AREA_FRONTEND, true);
        $quote = $this->getQuoteService->getQuote((int)$cartId);
        try {
            $this->addInstallationProductToQuoteService->add($quote, $installationData);
            $this->messageManager->addSuccessMessage(__('Installation elements service was added.'));
        } catch (NoSuchEntityException $e) {
        }
        $this->emulation->stopEnvironmentEmulation();
    }

    public function addProductForGuest(string $cartId, string $storeId, InstallationInterface $installationData): void
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $this->quoteIdMaskResource->load($quoteIdMask, $cartId, 'masked_id');
        $quoteId = $quoteIdMask->getData('quote_id');
        $this->addProduct($quoteId, $storeId, $installationData);
    }
}