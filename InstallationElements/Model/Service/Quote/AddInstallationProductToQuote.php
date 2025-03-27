<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\Quote;


use BelVG\InstallationElements\Api\Data\AdditionalPriceInterface;
use BelVG\InstallationElements\Api\Data\InstallationInterface;
use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use BelVG\InstallationElements\Model\Service\InstallationPriceCalculator;
use BelVG\LayoutQuoteItemImg\Helper\Data;
use BelVG\LayoutQuoteItemImg\Model\Service\Converter\Base64Converter as Base64ImgConverter;
use BelVG\MasterAccount\Api\Service\QuoteItemInterface as MaQuoteItemData;
use BelVG\MasterAccount\Model\Service\GetImageForProduct as GetImageForProductService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use BelVG\InstallationElements\Model\Service\GetInstallationProductFromQuote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class AddInstallationProductToQuote
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected InstallationProductConfig $installationProductConfig,
        protected InstallationPriceCalculator $installationPriceCalculator,
        protected CartRepositoryInterface $cartRepository,
        protected GetInstallationProductFromQuote $getInstallationProductFromQuoteService,
        protected DataObjectFactory $dataObjectFactory,
        protected PriceCurrencyInterface $priceCurrency,
        protected GetImageForProductService $getImageForProductService,
        protected Base64ImgConverter $base64ImgConverter,
        protected SerializerInterface $serializer
    ){

    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function add(CartInterface $quote,  InstallationInterface $installationData){
        $priceData = $this->installationPriceCalculator->calculate($quote, $installationData);
        $request = [
            'qty'                   => 1,
            'construction_waste'    => $installationData->getDisposalOfConstructionWaste(),
            'internal_finish'       => $installationData->getInternalFinish(),
            'internal_finish_type'  => $installationData->getInternalFinishType(),
            'living_room_qty'       => $installationData->getInstallationLivingRoomQty(),
            'high_ground_floor_qty' => $installationData->getInstallationHighGroundFloorQty(),
            'first_floor_qty'       => $installationData->getInstallationFirstFloorQty(),
            'conditions_approved'   => $installationData->getConditionsApproved()
        ];

        $item = $this->getInstallationProductFromQuoteService->get($quote);
        if (!$item) {
            $product = $this->productRepository->get($this->installationProductConfig->getProductSku());
            $request[Data::QUOTE_ITEM_IMG_INPUT ] = $this->getProductImgBasedOnQuoteItem($product);
            $this->addProductOptions($quote,$product, $request, $priceData);
            $quote->addProduct($product, $this->buildAddProductRequest($product, $request));
        } else {
            $this->handlePrice($item, $priceData);
            $this->addQuoteItemOptions($item, $quote, $priceData);
        }

        $this->cartRepository->save($quote);
    }

    private function getProductImgBasedOnQuoteItem(
        ProductInterface $product
    ) :string {
        $filePath = $this->getImageForProductService->getProductImagePath($product, 'cart_page_product_thumbnail');
        if ($filePath && is_file($filePath)) {
            return $this->base64ImgConverter->convert(file_get_contents($filePath), $filePath);
        }

        return '';
    }

    private function buildAddProductRequest(
        ProductInterface $product,
        iterable $requestData
    ) :\Magento\Framework\DataObject {
        $request = $this->dataObjectFactory->create();
        $request->setData($requestData);

        return $request;
    }

    private function addProductOptions(
        $quote,
        ProductInterface $product,
        iterable $request,
        array $priceData
    ): void {
        $installationCustomValues = $this->serializer->serialize($this->getCustomOptions($quote, $priceData));
        $product->addCustomOption('additional_options', $installationCustomValues);
        $product->addCustomOption('additional_factory_options', $installationCustomValues);

        $info_buyRequest = $request;
        $product->addCustomOption('info_buyRequest', $this->serializer->serialize($info_buyRequest));
    }

    public function getCustomOptions(
        $quote,
        array $priceData
    ): array {
        $customOptions = [];

        $allProductsQty = ($priceData['living_room_qty'] ?? 0)
            + ($priceData['high_ground_floor_qty'] ?? 0)
            + ($priceData['first_floor_qty'] ?? 0);
        if ($priceData['base_price'] ?? false) {
            $customOptions[]
                = [
                'label'           => (string)__('Assembly'),
                'code'            => 'base_price',
                'price'           => $priceData['base_price'] ?? 0,
                'formatted_price' =>
                    $this->priceCurrency->format(
                        $priceData['base_price'] ?? 0,
                        true,
                        PriceCurrencyInterface::DEFAULT_PRECISION,
                        $quote->getStore()
                    ),
                'hidden'          => false,
                'value'           => $priceData['base_price'] ?? 0,
                'qty'             => $allProductsQty
            ];
        }

        $customOptions[]
            = [
            'label'           => (string)__('Conditions approved'),
            'code'            => 'conditions_approved',
            'hidden'          => true,
            'value'           => $priceData['conditions_approved'] ?? false,
            'formatted_value' => __('Yes')
        ];

        if ($priceData['construction_price_included'] ?? false) {
            $customOptions[]
                = [
                'label'           => (string)__('Disposal of construction waste'),
                'code'            => 'construction_waste',
                'hidden'          => false,
                'price'           => $priceData['construction_price'] ?? 0,
                'formatted_price' =>
                    $this->priceCurrency->format(
                        $priceData['construction_price'] ?? 0,
                        true,
                        PriceCurrencyInterface::DEFAULT_PRECISION,
                        $quote->getStore()
                    ),
                'value'           => __('Yes'),
                'formatted_value' => __('Yes'),
                'qty'             => $allProductsQty
            ];
        }

        if ($priceData['internal_finish_price_included'] ?? false) {
            $customOptions[]
                = [
                'label'           => (string)__("Internal Finish ({$priceData['internal_finish_type']})"),
                'code'            => 'internal_finish',
                'hidden'          => false,
                'price'           => $priceData['internal_finish_price'] ?? 0,
                'formatted_price' =>
                    $this->priceCurrency->format(
                        $priceData['internal_finish_price'] ?? 0,
                        true,
                        PriceCurrencyInterface::DEFAULT_PRECISION,
                        $quote->getStore()
                    ),
                'type'            => $priceData['internal_finish_type'],
                'value'           => __('Yes'),
                'formatted_value' => __('Yes'),
                'qty'             => $allProductsQty
            ];
        }

        if (($priceData['living_room_qty'] ?? 0) !== 0) {
            $price = $this->priceCurrency->format(
                    $priceData['living_room_price'] ?? 0,
                    false,
                    PriceCurrencyInterface::DEFAULT_PRECISION,
                    $quote->getStore()
                );

            $customOptions[]
                = [
                'label'           => (string)__('Living room qty'),
                'code'            => 'living_room_qty',
                'hidden'          => false,
                'price'           => $priceData['living_room_price'] ?? 0,
                'formatted_price' => $price,
                'value'           => $priceData['living_room_qty'],
                'formatted_value' => $priceData['living_room_qty'] . ' ' . __('qty.'),
                'qty'             => $priceData['living_room_qty']
            ];
        }

        if (($priceData['high_ground_floor_qty'] ?? 0) !== 0) {
            $price = $this->priceCurrency->format(
                $priceData['high_ground_floor_price'] ?? 0,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $quote->getStore()
            );

            $customOptions[]
                = [
                'label'           => (string)__('High ground floor qty'),
                'code'            => 'high_ground_floor_qty',
                'hidden'          => false,
                'price'           => $priceData['high_ground_floor_price'] ?? 0,
                'formatted_price' => $price,
                'value'           => $priceData['high_ground_floor_qty'],
                'formatted_value' => $priceData['high_ground_floor_qty'] . ' ' . __('qty.'),
                'qty'             => $priceData['high_ground_floor_qty']
            ];
        }

        if (($priceData['first_floor_qty'] ?? 0) !== 0) {
            $price = $this->priceCurrency->format(
                $priceData['first_floor_price'] ?? 0,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $quote->getStore()
            );
            $customOptions[]
                = [
                'label'           => (string)__('First floor qty'),
                'code'            => 'first_floor_qty',
                'hidden'          => false,
                'price'           => $priceData['first_floor_price'] ?? 0,
                'formatted_price' => $price,
                'value'           => $priceData['first_floor_qty'],
                'formatted_value' => $priceData['first_floor_qty'] . ' ' . __('qty.'),
                'qty'             => $priceData['first_floor_qty']
            ];
        }

        if (($priceData['driving_price'] ?? 0) !== 0) {
            $customOptions[]
                = [
                'label'           => (string)__('Driving'),
                'code'            => 'driving_price',
                'hidden'          => false,
                'price'           => $priceData['driving_price'] ?? 0,
                'formatted_price' =>
                    $this->priceCurrency->format(
                        $priceData['driving_price'] ?? 0,
                        true,
                        PriceCurrencyInterface::DEFAULT_PRECISION,
                        $quote->getStore()
                    ),
                'value'           => true
            ];
        }
        if (count($priceData['additional_prices']) > 0) {
            /** @var AdditionalPriceInterface $additionalPrice */
            foreach ($priceData['additional_prices'] as $additionalPrice){
                $formattedValue = $this->priceCurrency->format(
                        $additionalPrice->getPrice(),
                        true,
                        PriceCurrencyInterface::DEFAULT_PRECISION,
                        $quote->getStore()
                    );
                $customOptions[]
                    = [
                    'label'           => $additionalPrice->getLabel(),
                    'code'            => $additionalPrice->getCode(),
                    'hidden'          => false,
                    'price'           => $additionalPrice->getPrice(),
                    'formatted_price' => $formattedValue,
                    'formatted_value' => $formattedValue,
                    'value'           => $this->priceCurrency->format(
                        $additionalPrice->getPrice(),
                        false,
                        PriceCurrencyInterface::DEFAULT_PRECISION,
                        $quote->getStore()
                    )
                ];
            }
        }

        return $customOptions;
    }

    /**
     * @throws LocalizedException
     */
    public function addQuoteItemOptions(
        QuoteItem $quoteItem,
        CartInterface $quote,
        $priceData
    ): void {
        $installationCustomValues = $this->serializer->serialize($this->getCustomOptions($quote, $priceData));
        $quoteItem->addOption(array(
            'product_id' => $quoteItem->getProductId(),
            'code' => 'additional_options',
            'value' => $installationCustomValues
        ));
        $quoteItem->addOption(array(
            'product_id' => $quoteItem->getProductId(),
            'code' => 'additional_factory_options',
            'value' => $installationCustomValues
        ));
    }

    /**
     * @throws NoSuchEntityException
     */
    public function handlePrice(
        QuoteItem $quoteItem,
        $priceData
    ): void {
        $itemPrice = $priceData['price'];
        $quoteItem->setData(MaQuoteItemData::CUSTOM_PRICE, $itemPrice);
        $quoteItem->setData(MaQuoteItemData::LOCKED, false);
        $quoteItem->setCustomPrice($itemPrice);
        $quoteItem->setOriginalCustomPrice($itemPrice);
        $quoteItem->getProduct()->setIsSuperMode(true);
    }
}