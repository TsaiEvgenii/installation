<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Model\Quote\Item\Processor;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use BelVG\InstallationElements\Api\Data\InstallationInterfaceFactory;
use BelVG\InstallationElements\Model\Service\InstallationPriceCalculator;
use BelVG\InstallationElements\Model\Service\Quote\AddInstallationProductToQuote;
use BelVG\InstallationElements\Model\Service\RetrieveAdditionalPricesFromCustomOptions;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\Quote\Item;
use Psr\Log\LoggerInterface;

class HandleQuoteItemForInstallationProduct
{
    private const LOG_PREFIX = '[BelVG_MeasurementRequest::SetQuoteItemRowTotalPricePlugin]: ';

    public function __construct(
        protected InstallationProductConfig $installationProductConfig,
        protected InstallationInterfaceFactory $installationInterfaceFactory,
        protected AddInstallationProductToQuote $addInstallationProductToQuoteService,
        protected InstallationPriceCalculator $installationPriceCalculator,
        protected RetrieveAdditionalPricesFromCustomOptions $retrieveAdditionalPricesFromCustomOptionsService,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger
    ) {

    }

    public function afterPrepare(
        $source,
        $result,
        Item $item,
        DataObject $request,
        Product $candidate
    ) {
        try {
            if ($candidate->getSku() == $this->installationProductConfig->getProductSku()) {
                $request['qty'] = $item->getQty();
                /** @var  \BelVG\InstallationElements\Api\Data\InstallationInterface $installationData */
                $installationData = $this->installationInterfaceFactory->create();
                $installationData->setDisposalOfConstructionWaste($request['construction_waste'] ?? false);
                $installationData->setInternalFinish($request['internal_finish'] ?? false);
                $installationData->setInternalFinishType($request['internal_finish_type'] ?? '');
                $installationData->setInstallationLivingRoomQty($request['living_room_qty'] ?? 0);
                $installationData->setInstallationFirstFloorQty($request['first_floor_qty'] ?? 0);
                $installationData->setInstallationHighGroundFloorQty($request['high_ground_floor_qty'] ?? 0);
                $installationData->setInstallationAboveFirstFloorQty(0);
                //AdditionalPrices for installation product
                $productCustomOptions = $candidate->getCustomOptions();
                if ($additionalOptions = ($productCustomOptions['additional_options'] ?? false)) {
                    $additionalOptions = $this->serializer->unserialize($additionalOptions->getValue() ?? '[]');
                    $additionalPrices = $this->retrieveAdditionalPricesFromCustomOptionsService->retrieve($additionalOptions);
                    $installationData->setAdditionalPrices($additionalPrices);
                }

                $priceData = $this->installationPriceCalculator->calculate($item->getQuote(), $installationData);

                $this->addInstallationProductToQuoteService->handlePrice($item, $priceData);
                $this->addInstallationProductToQuoteService->addQuoteItemOptions($item, $item->getQuote(), $priceData);
                $item->setData('description', $candidate->getShortDescription());
                $item->setData('name', $candidate->getName());
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

    }
}