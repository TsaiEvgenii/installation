<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Observer;


use BelVG\InstallationElements\Api\Data\InstallationInterfaceFactory;
use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use BelVG\InstallationElements\Model\Service\GetInstallationProductFromQuote;
use BelVG\InstallationElements\Model\Service\InstallationPriceCalculator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use BelVG\MasterAccount\Api\Service\QuoteItemInterface as MaQuoteItemData;
use Psr\Log\LoggerInterface;

class MergeInstallationServiceProducts implements ObserverInterface
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::MergeInstallationServiceProductsObserver]: ';
    public function __construct(
        protected InstallationProductConfig $installationProductConfig,
        protected GetInstallationProductFromQuote $getInstallationProductFromQuote,
        protected InstallationPriceCalculator $installationPriceCalculator,
        protected InstallationInterfaceFactory $installationDataFactory,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger
    ){
    }

    public function execute(Observer $observer)
    {
        try {
            $quote = $observer->getData('quote');
            $source = $observer->getData('source');

            $sourceInstallationItem = $this->getInstallationProductFromQuote->get($source);
            if ($sourceInstallationItem === null) {
                return;
            }

            $quoteInstallationItem = $this->getInstallationProductFromQuote->get($quote);
            if ($quoteInstallationItem === null) {
                return;
            }

            $additionalOptionsSourceItem = $sourceInstallationItem->getOptionByCode('additional_options');
            $additionalOptionsQuoteItem = $quoteInstallationItem->getOptionByCode('additional_options');

            $additionalOptionsSourceItem = $this->serializer->unserialize(
                $additionalOptionsSourceItem
                    ?
                    $additionalOptionsSourceItem->getValue() ?? '[]'
                    :
                    '[]'
            );
            $additionalOptionsQuoteItem = $this->serializer->unserialize(
                $additionalOptionsQuoteItem
                    ?
                    $additionalOptionsQuoteItem->getValue() ?? '[]'
                    :
                    '[]'
            );

            //Merger options
            foreach ($additionalOptionsSourceItem as $additionalOptionSourceItem) {
                $match = false;
                $code = $additionalOptionSourceItem['code'] ?? '';
                if ($code === 'base_price') {
                    continue;
                }
                foreach ($additionalOptionsQuoteItem as &$additionalOptionQuoteItem) {
                    if ($code === $additionalOptionQuoteItem['code']) {
                        $match = true;
                        if (str_contains($code, 'qty')) {
                            $additionalOptionQuoteItem['value'] += $additionalOptionSourceItem['value'];
                            $additionalOptionQuoteItem['formatted_value']
                                = $additionalOptionQuoteItem['value'] . ' ' . __('qty.');
                        }
                        break;
                    }
                }
                if ($match === false) {
                    $additionalOptionsQuoteItem[] = $additionalOptionSourceItem;
                }
            }

            $installationCustomValues = $this->serializer->serialize($additionalOptionsQuoteItem);
            $quoteInstallationItem->addOption(array(
                'product_id' => $quoteInstallationItem->getProductId(),
                'code'       => 'additional_options',
                'value'      => $installationCustomValues
            ));
            $quoteInstallationItem->addOption(array(
                'product_id' => $quoteInstallationItem->getProductId(),
                'code'       => 'additional_factory_options',
                'value'      => $installationCustomValues
            ));

            //Set new price
            /** @var \BelVG\InstallationElements\Api\Data\InstallationInterface $installationData */
            $installationData = $this->installationDataFactory->create();
            foreach ($additionalOptionsQuoteItem as $item) {
                $code = $item['code'] ?? '';
                switch ($code) {
                    case 'construction_waste':
                        $installationData->setDisposalOfConstructionWaste(true);
                        break;
                    case 'internal_finish':
                        $installationData->setInternalFinish(true);
                        $installationData->setInternalFinishType($item['type'] ?? '');
                        break;
                    case 'high_ground_floor_qty':
                        $installationData->setInstallationHighGroundFloorQty((int)$item['value']);
                        break;
                    case 'first_floor_qty':
                        $installationData->setInstallationFirstFloorQty((int)$item['value']);
                        break;
                    case 'living_room_qty':
                        $installationData->setInstallationLivingRoomQty((int)$item['value']);
                        break;
                }
            }
            $installationPriceData = $this->installationPriceCalculator->calculate($quote, $installationData);
            $itemPrice = $installationPriceData['price'];
            $quoteInstallationItem->setData(MaQuoteItemData::CUSTOM_PRICE, $itemPrice);
            $quoteInstallationItem->setData(MaQuoteItemData::LOCKED, false);
            $quoteInstallationItem->setCustomPrice($itemPrice);
            $quoteInstallationItem->setOriginalCustomPrice($itemPrice);
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
    }
}