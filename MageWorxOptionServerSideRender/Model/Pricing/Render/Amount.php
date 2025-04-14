<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Pricing\Render;

use BelVG\MageWorxOptionServerSideRender\Model\Helper\Data;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedHeight;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedWidth;
use BelVG\LayoutMaterial\Model\Service\IndexedPriceService;
use BelVG\SaleCountdown\Api\Locator\GetActualRuleInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * @api
 */
class Amount extends \BelVG\ProductPriceDisplay\Pricing\Render\Amount
{
    /**
     * @var GetActualRuleInterface
     */
    protected $getActualRuleService;

    protected $mageworxHelper;
    /**
     * @var Template\Context
     */
    protected $context;
    /**
     * @var
     */
    protected $priceValue;
    private GetSelectedHeight $selectedHeight;
    private GetSelectedWidth $selectedWidth;
    private IndexedPriceService $indexedPriceService;
    private $priceDiscountValue = null;

    /**
     * @param Context $context
     * @param AmountInterface $amount
     * @param PriceCurrencyInterface $priceCurrency
     * @param RendererPool $rendererPool
     * @param Data $mageworxHelper
     * @param GetActualRuleInterface $getActualRuleService
     * @param GetSelectedHeight $selectedHeight
     * @param GetSelectedWidth $selectedWidth
     * @param IndexedPriceService $indexedPriceService
     * @param SaleableInterface|null $saleableItem
     * @param PriceInterface|null $price
     * @param array $data
     */
    public function __construct(
        Context $context,
        AmountInterface $amount,
        PriceCurrencyInterface $priceCurrency,
        RendererPool $rendererPool,
        Data $mageworxHelper,
        GetActualRuleInterface $getActualRuleService,
        GetSelectedHeight $selectedHeight,
        GetSelectedWidth $selectedWidth,
        IndexedPriceService $indexedPriceService,
        SaleableInterface $saleableItem = null,
        PriceInterface $price = null,
        array $data = []
    ) {
        $this->getActualRuleService = $getActualRuleService;
        $this->mageworxHelper = $mageworxHelper;
        $this->context = $context;

        parent::__construct($context, $amount, $priceCurrency, $rendererPool, $saleableItem, $price, $mageworxHelper, $getActualRuleService, $data);
        $this->selectedHeight = $selectedHeight;
        $this->selectedWidth = $selectedWidth;
        $this->indexedPriceService = $indexedPriceService;
    }


    /**
     * @return float|int|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDisplayValue()
    {
        if ($this->priceValue !== null) {
            return $this->priceValue;
        } else {
            $idxPrice = $this->indexedPriceService->getIdxPrice($this->saleableItem);
            if ($idxPrice) {
                $this->priceDiscountValue = $idxPrice;
                return $this->priceDiscountValue;
            }

            if ($this->saleableItem && $this->saleableItem instanceof ProductInterface) {
                $layoutData = $this->prepareLayoutData();
                $this->priceValue = $this->mageworxHelper->getOptionsPriceWithDiscount(
                    $this->saleableItem,
                    $this->context,
                    $layoutData
                );
                return $this->priceValue;
            } else {
                return $this->getAmount()->getValue();
            }
        }
    }

    public function getDisplayValueWithoutDiscont()
    {
        if ($this->priceDiscountValue !== null) {
            return $this->priceDiscountValue;
        } else {
            $idxPrice = $this->indexedPriceService->getIdxPrice($this->saleableItem, 'prices_without_discount');
            if ($idxPrice) {
                $this->priceDiscountValue = $idxPrice;
                return $this->priceDiscountValue;
            }

            if ($this->saleableItem && $this->saleableItem instanceof ProductInterface) {
                $layoutData = $this->prepareLayoutData();
                $this->priceDiscountValue = $this->mageworxHelper->getOptionsPrice(
                    $this->saleableItem,
                    $this->context,
                    $layoutData
                );
                return $this->priceDiscountValue;
            } else {
                return $this->getAmount()->getValue();
            }
        }
    }

    public function getProductQty()
    {
        if($this->saleableItem && $this->saleableItem instanceof ProductInterface){
            $preconfiguredValuesQty = $this->saleableItem->getPreconfiguredValues()->getQty();
            return $preconfiguredValuesQty ?: 1;
        }
        return 1;
    }

    private function prepareLayoutData() :array
    {
        return ['width'=>$this->selectedWidth->get(), 'height'=>$this->selectedHeight->get()];
    }
}
