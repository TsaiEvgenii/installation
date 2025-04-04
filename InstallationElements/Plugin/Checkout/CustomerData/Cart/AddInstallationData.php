<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Checkout\CustomerData\Cart;


use BelVG\InstallationElements\Api\Data\InstallationInterfaceFactory;
use BelVG\InstallationElements\Model\Service\GetInstallationProductFromQuote;
use BelVG\InstallationElements\Model\Service\GetQuoteQty;
use BelVG\InstallationElements\Model\Service\InstallationPriceCalculator;
use BelVG\InstallationElements\Model\Service\RetrieveAdditionalPricesFromCustomOptions;
use BelVG\MeasurementRequest\Model\Service\ProductForm\PriceEstimator;
use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

class AddInstallationData
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::AddInstallationDataPlugin]: ';
    private const KEY = 'belvg_installation_data';

    protected ?Quote $quote = null;

    public function __construct(
        protected CheckoutSession $checkoutSession,
        protected InstallationInterfaceFactory $installationInterfaceFactory,
        protected GetInstallationProductFromQuote $getInstallationProductFromQuoteService,
        protected GetQuoteQty $getQuoteQtyService,
        protected InstallationPriceCalculator $installationPriceCalculator,
        protected RetrieveAdditionalPricesFromCustomOptions $retrieveAdditionalPricesFromCustomOptionsService,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger,
        protected PriceEstimator $measurementPriceEstimator
    ){}


    public function afterGetSectionData(
        Cart $subject,
        $result
    ) {
        try {
            $currentQuote = $this->getQuote();
            $itemQty = $this->getQuoteQtyService->get($currentQuote);
            $placeOrderAllowed = true;
            $installationItem = $this->getInstallationProductFromQuoteService->get($currentQuote);
            /** @var  \BelVG\InstallationElements\Api\Data\InstallationInterface $installationData */
            $installationData = $this->installationInterfaceFactory->create();
            $installationData->setDisposalOfConstructionWaste(false);
            $installationData->setInternalFinish(false);
            $installationData->setInstallationLivingRoomQty($itemQty);
            $installationData->setInstallationFirstFloorQty(0);
            $installationData->setInstallationHighGroundFloorQty(0);
            $installationData->setInstallationAboveFirstFloorQty(0);
            if ($installationItem) {
                $installationData->setInstallationLivingRoomQty(0);
                $additionalOptions = $installationItem->getOptionByCode('additional_options');
                $additionalOptions = $this->serializer->unserialize(
                    $additionalOptions
                        ?
                        $additionalOptions->getValue() ?? '[]'
                        :
                        '[]'
                );
                $placeOrderAllowed = $this->numberOfItemsMatched($itemQty, $additionalOptions);
                foreach ($additionalOptions as $additionalOption) {
                    if (($additionalOption['code'] ?? '') === 'conditions_approved') {
                        $installationData->setConditionsApproved(true);
                    }
                    if (($additionalOption['code'] ?? '') === 'construction_waste') {
                        $installationData->setDisposalOfConstructionWaste(true);
                    }
                    if (($additionalOption['code'] ?? '') === 'internal_finish') {
                        $installationData->setInternalFinish(true);
                        $installationData->setInternalFinishType($additionalOption['type']);
                    }
                    if (($additionalOption['code'] ?? '') === 'living_room_qty') {
                        $installationData->setInstallationLivingRoomQty((int)$additionalOption['value']);
                    }
                    if (($additionalOption['code'] ?? '') === 'high_ground_floor_qty') {
                        $installationData->setInstallationHighGroundFloorQty((int)$additionalOption['value']);
                    }
                    if (($additionalOption['code'] ?? '') === 'first_floor_qty') {
                        $installationData->setInstallationFirstFloorQty((int)$additionalOption['value']);
                    }
                }
                $additionalPrices = $this->retrieveAdditionalPricesFromCustomOptionsService->retrieve($additionalOptions);
                $installationData->setAdditionalPrices($additionalPrices);
            }
            $installationPriceData = $this->installationPriceCalculator->calculate(
                $currentQuote,
                $installationData
            );

            $measurementPrice = 0;
            try {
                $measurementPrice = $this->measurementPriceEstimator->getPrice(['qty' => $itemQty]);
            } catch (\Throwable $e) {
                $this->logger->warning(
                    sprintf(
                        self::LOG_PREFIX . ' Error calculating measurement price: %s',
                        $e->getMessage()
                    ),
                    $e->getTrace()
                );
            }

            $result[self::KEY] = [
                ...$installationPriceData,
                ...[
                    'added_item_id'          => $installationItem?->getId(),
                    'qty'                    => $itemQty,
                    'is_place_order_allowed' => $placeOrderAllowed,
                    'measurement_price'      => $measurementPrice,
                ]
            ];

        } catch (\Throwable $t) {
            $this->logger->warning(
                sprintf(
                    self::LOG_PREFIX . ' something went wrong: %s',
                    $t->getMessage()
                ),
                $t->getTrace()
            );
        }

        return $result;
    }

    protected function getQuote(): CartInterface|Quote|null
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    protected function numberOfItemsMatched(int $quoteItemQty, $additionalOptions): bool
    {
        $installationItemQty = 0;

        foreach ($additionalOptions as $additionalOption) {
            if (
                ($additionalOption['code'] ?? '') === 'living_room_qty'
                || ($additionalOption['code'] ?? '') === 'high_ground_floor_qty'
                || ($additionalOption['code'] ?? '') === 'first_floor_qty'
            ) {
                $installationItemQty += (int)($additionalOption['value'] ?? 0);
            }
        }

        return $quoteItemQty === $installationItemQty;
    }
}