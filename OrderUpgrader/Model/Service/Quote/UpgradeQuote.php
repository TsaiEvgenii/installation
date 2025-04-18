<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);

namespace BelVG\OrderUpgrader\Model\Service\Quote;

use BelVG\AmastyCouponsExtend\Model\RuleRegistry;
use BelVG\LayoutQuoteItemImg\Api\QuoteItemImgRepositoryInterface;
use BelVG\LayoutQuoteItemImg\Helper\Data;
use BelVG\MadeInDenmark\Model\Service\Quote\GetQuoteService;
use BelVG\OrderFactory\Model\Product\DeliveryEstimator;
use BelVG\OrderFactory\Model\Service\QuoteItemDelivery as QuoteItemDeliveryService;
use BelVG\OrderUpgrader\Api\Webapi\UpgradeQuoteInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;

class UpgradeQuote implements UpgradeQuoteInterface
{
    private const LOG_PREFIX = '[BelVG_OrderUpgrader::UpgradeQuoteWebapiService]: ';

    public function __construct(
        private GetOptionsToUpgradeForQuoteService $getOptionsToUpgradeForQuoteService,
        private ProductRepositoryInterface $productRepository,
        private CartRepositoryInterface $cartRepository,
        private DeliveryEstimator $productDeliveryEstimator,
        private QuoteItemDeliveryService $quoteItemDeliveryService,
        private RuleRegistry $ruleRegistry,
        private ManagerInterface $messageManager,
        private GetQuoteService $getQuoteService,
        private \Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask $quoteIdMaskResource,
        private QuoteIdMaskFactory $quoteIdMaskFactory,
        private Emulation $emulation,
        private State $appState,
        private SerializerInterface $serializer,
        private QuoteItemImgRepositoryInterface $quoteItemImgRepository,
        private LoggerInterface $logger
    ) {
    }

    public function upgradeQuote($cartId, $storeId, $parameters): void
    {
        $this->executeWithEmulation($cartId, $storeId, 'emulationCallback', $parameters);
    }

    public function upgradeQuoteForGuest($cartId, $storeId, $parameters): void
    {
        $this->executeWithEmulation($cartId, $storeId, 'emulationCallbackForGuest', $parameters);
    }

    private function executeWithEmulation($cartId, $storeId, string $callbackMethod, ...$callbackParams): void
    {
        $this->emulation->startEnvironmentEmulation((int)$storeId, Area::AREA_FRONTEND, true);

        try {
            $this->appState->emulateAreaCode(
                Area::AREA_FRONTEND,
                [
                    $this,
                    $callbackMethod
                ],
                array_merge([
                    $cartId,
                    $storeId
                ], $callbackParams)
            );
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                '%s Error: %s, Trace: %s',
                self::LOG_PREFIX,
                $t->getMessage(),
                PHP_EOL . $t->getTraceAsString()
            ));
        } finally {
            $this->emulation->stopEnvironmentEmulation();
        }
    }

    public function emulationCallback($cartId, $storeId, $parameters): void
    {
        $this->upgrade(
            $this->getQuoteService->getQuote((int)$cartId),
            $storeId,
            $parameters
        );
    }

    public function emulationCallbackForGuest($cartId, $storeId, $parameters): void
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $this->quoteIdMaskResource->load($quoteIdMask, $cartId, 'masked_id');

        if (!$quoteIdMask->getId()) {
            $this->logger->error(self::LOG_PREFIX . " QuoteIdMask not found for masked_id: $cartId");
            return;
        }

        $quoteId = (int)$quoteIdMask->getData('quote_id');
        $quoteObj = $this->getQuoteService->getQuote($quoteId);

        if (!$quoteObj || !$quoteObj->getId()) {
            $this->logger->error(self::LOG_PREFIX . " Quote not found for quote_id: $quoteId");
            return;
        }

        $this->upgrade($quoteObj, $storeId, $parameters);
    }

    public function upgrade($quote, $storeId, $parameters): void
    {
        $addedItems = [];
        $materialsDataStructure = $this->getOptionsToUpgradeForQuoteService->getMaterialsDataStructure($quote);
        $materialId = $this->getParameterValue($parameters, 'material');
        $energyClass = $this->getParameterValue($parameters, 'energy_class');
        if ($materialId === null && $energyClass === null) {
            return;
        }
        foreach ($materialsDataStructure as $quoteItemId => $structure) {
            $materials = $structure['materials'] ?? [];
            foreach ($materials as $material) {
                $options = [];
                if ($materialId === null && $material['current'] && $energyClass) {
                    $optionsData = $material['options_structure']['energy_class'][$energyClass] ?? [];
                    if ($optionsData['current_value']) {
                        continue;
                    }
                    $options = $optionsData['map'];
                }

                if ($materialId !== null && (int)$material['material_id'] === (int)$materialId && $material['current'] === false) {
                    if ($energyClass) {
                        $optionsData = $material['options_structure']['energy_class'][$energyClass] ?? [];
                        $currentEnergyClassValue = $optionsData['current_value'];
                        $options = $optionsData['map'];
                    } else {
                        $options = $material['options_map'] ?? [];
                    }
                }

                if (count($options) === 0) {
                    continue;
                }
                $productId = $material['product_id'];
                $qty = $structure['qty'];
                $request = new DataObject();
                $request->setData('product', $productId);
                $request->setData('qty', $qty);
                $request->setData('options', $options);

                //Set image
                try {
                    $quoteItemImg = $this->quoteItemImgRepository->getByQuoteItemId($quoteItemId);
                    $request->setData(Data::QUOTE_ITEM_IMG_INPUT, $quoteItemImg->getQuoteItemImg());
                } catch (\Exception $e) {
                    // do nothing
                }

                $altProduct = $this->productRepository->getById($productId);
                $item = $quote->addProduct($altProduct, $request);
                if (is_string($item)) {
                    throw new \RuntimeException(self::LOG_PREFIX . '[apply] ' . $item);
                }

                $oldSku = $structure['product_sku'];
                $newSku = $altProduct->getSku();
                if ($oldSku === $newSku) {
                    $this->messageManager->addNoticeMessage(__('Product %1 was updated', $oldSku));
                } else {
                    $this->messageManager->addNoticeMessage(__('Product %1 was replaced with product %2', $oldSku, $newSku));
                }

                $addedItems[] = $item;
                if ($item instanceof QuoteItem) {
                    $this->copyInfo($item, $quote->getItemById($quoteItemId));
                    //Delete original quote item
                    $quote->removeItem($quoteItemId);
                }
            }
        }

        if (count($addedItems) === 0) {
            return;
        }
        $this->cartRepository->save($quote);

        //Save delivery week for quote items
        foreach ($addedItems as $addedItem) {
            $deliveryData = $this->productDeliveryEstimator->getDeliveryWeeks($addedItem->getProduct());
            $deliveryNumber = $deliveryData['number'] ?? [];
            if (count($deliveryNumber) === 0) {
                $deliveryWeeks = '[]';
            } else {
                $deliveryWeeks = $this->serializer->unserialize(current($deliveryNumber));
            }
            $this->quoteItemDeliveryService->saveQuoteItemDelivery($addedItem, $deliveryWeeks);
        }

        $this->ruleRegistry->resetRegistry($quote->getId());
    }

    /**
     * @param \BelVG\OrderUpgrader\Model\Data\UpgradeParameter[] $params
     *
     * @return string|null
     */
    protected function getParameterValue(array $params, $paramCode): ?string
    {
        $paramValue = null;
        foreach ($params as $param) {
            if ($param->getCode() === $paramCode) {
                $paramValue = $param->getValue();
                break;
            }
        }
        return $paramValue;
    }
    private function copyInfo(QuoteItem $newAlternativeItem, bool|QuoteItem $oldItem): void
    {
        if ($oldItem) {
            $newAlternativeItem->setNbr($oldItem->getNbr());
        }
    }
}
