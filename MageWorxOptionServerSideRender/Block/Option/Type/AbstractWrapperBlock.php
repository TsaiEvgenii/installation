<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 * @method  getOption()
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Block\Option\Type;

use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Config;
use BelVG\MageWorxOptionServerSideRender\Model\ProductRegistry;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetAdditionalOptionValueInformation;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\ParserInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedOptionProcessor;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\PriceDiscountInterface;
use Magento\Catalog\Model\Product\Option\ValueFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Pricing\Render\Amount;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\Data\StoreInterface;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use Psr\Log\LoggerInterface;

abstract class AbstractWrapperBlock extends Template implements RenderBlockInterface
{
    use SelectedOptionProcessor;

    protected ParserInterface $parser;

    protected $valueId;

    protected GetAdditionalOptionValueInformation $getAdditionalOptionValueInformation;
    protected LoggerInterface $logger;
    private Amount $amount;
    protected ValueFactory $valueFactory;
    /**
     * @var \BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface[]|iterable
     */
    protected iterable $selectedOptions;
    private Data $priceHelper;
    private PriceDiscountInterface $priceDiscount;

    /**
     * Radio constructor.
     * @param Template\Context $context
     * @param GetAdditionalOptionValueInformation $getAdditionalOptionValueInformation
     * @param ParserInterface $parser
     * @param LoggerInterface $logger
     * @param Amount $amount
     * @param ValueFactory $valueFactory
     * @param Data $priceHelper
     * @param PriceDiscountInterface $priceDiscount
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        GetAdditionalOptionValueInformation $getAdditionalOptionValueInformation,
        ParserInterface $parser,
        LoggerInterface $logger,
        Amount $amount,
        ValueFactory $valueFactory,
        Data $priceHelper,
        PriceDiscountInterface $priceDiscount,
        GetSelectedOptions $selectedOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->parser = $parser;
        $this->getAdditionalOptionValueInformation = $getAdditionalOptionValueInformation;
        $this->logger = $logger;
        $this->amount = $amount;
        $this->valueFactory = $valueFactory;
        $this->selectedOptions = $selectedOptions;
        $this->priceHelper = $priceHelper;
        $this->priceDiscount = $priceDiscount;
    }

    public function isValid()
    {
        return \is_object($this->getOption()->getValueById($this->getValueId()));
    }

    /**
     * @return \Magento\Catalog\Model\Product\Option\Value|DataObject
     */
    public function getOptionValue()
    {
        if ($this->isValid()) {
            return $this->getOption()->getValueById($this->getValueId());
        }
        return new DataObject(['images_data'=>'']);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        return $this->getOption()->getProduct();
    }

    public function setValueId(int $valueId)
    {
        $this->valueId = $valueId;
    }

    public function getValueId() :int
    {
        return (int)$this->valueId;
    }

    public function getImages() :iterable
    {
        $images = [];
        $value = $this->getOption()->getValueById($this->getValueId());
        $store = $this->getStore();
        if(is_object($value)){
          $images = $this->getOptionValueImages($value, $store);
        }
        return $images;
    }

    public function renderValuePrice()
    {
        return  $this->priceHelper->currencyByStore(
            $this->getPrice(),
            $this->_storeManager->getStore(),
            true,
            false
        );
    }



    abstract public function process(string $result) :string;

    public function getCurrentValue()
    {
        return $this->getSelectedValue($this->getOption(), $this->selectedOptions);
    }

    public function getValuePrice()
    {
        return $this->priceHelper->currencyByStore(
            $this->getPrice(),
            $this->_storeManager->getStore(),
            false
        );
    }

    protected function getPrice()
    {
        $price = $this->getCurrentValue()->getPrice(true);
        $value = $this->getCurrentValue();
        $priceWithDiscount = $this->priceDiscount->modifier((float)$price, $value->getProduct());
        return $priceWithDiscount;
    }

    protected function getLoggingData()
    {
        $product = $this->getProduct();
        $option = $this->getOption();
        $optionValue = $this->getOptionValue();
        return [
            'product_id'=>is_object($product) ? $product->getId() : '',
            'option_id' => is_object($option) ? $option->getId() : '',
            'option_value'=>is_object($optionValue) ? ['store_title'=>$optionValue->getData('store_title'),
                                                        'default_title'=>$optionValue->getData('default_title'),
                                                        'store_code' => $this->_storeManager->getStore()->getCode()] : []
        ];
    }

    private function getStore()
    {
        return $this->_storeManager->getStore();
    }

    private function getOptionValueImages($value, StoreInterface $store)
    {
        return $this->getAdditionalOptionValueInformation->get($value, (int)$store->getId())['images'] ?? [];
    }
}
