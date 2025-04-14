<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Block\Option\Type;

use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerData;
use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout;
use BelVG\MageWorxOptionServerSideRender\Model\Config;
use BelVG\MageWorxOptionServerSideRender\Model\ProductRegistry;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetAdditionalOptionValueInformation;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedHeight;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedWidth;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedSectionSizes;
use BelVG\MageWorxOptionServerSideRender\Model\Service\ParserInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\PriceDiscountInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\SelectedRequestOptionInterface;
use Magento\Catalog\Model\Product\Option\ValueFactory;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Pricing\Render\Amount;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;

class Text extends AbstractWrapperBlock implements ArgumentInterface
{
    private GetSelectedHeight $selectedHeight;
    private GetSelectedWidth $selectedWidth;
    private LayoutCustomizerData $layoutCustomizerData;
    private Layout $layoutProductData;
    private $layoutDefaultData = null;
    /**
     * @var GetSelectedSectionSizes
     */
    private GetSelectedSectionSizes $selectedSectionSizes;

    /**
     * Text constructor.
     * @param Template\Context $context
     * @param GetAdditionalOptionValueInformation $getAdditionalOptionValueInformation
     * @param ParserInterface $parser
     * @param LoggerInterface $logger
     * @param Amount $amount
     * @param ValueFactory $valueFactory
     * @param SelectedRequestOptionInterface $selectedRequestOption
     * @param Data $priceHelper
     * @param GetSelectedHeight $selectedHeight
     * @param GetSelectedWidth $selectedWidth
     * @param GetSelectedSectionSizes $selectedSectionSizes
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
        GetSelectedHeight $selectedHeight,
        GetSelectedWidth $selectedWidth,
        GetSelectedSectionSizes $selectedSectionSizes,
        LayoutCustomizerData $layoutCustomizerData,
        PriceDiscountInterface $priceDiscountService,
        GetSelectedOptions $selectedOptions,
        Layout $layoutProductData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $getAdditionalOptionValueInformation,
            $parser,
            $logger,
            $amount,
            $valueFactory,
            $priceHelper,
            $priceDiscountService,
            $selectedOptions,
            $data
        );
        $this->selectedHeight = $selectedHeight;
        $this->selectedWidth = $selectedWidth;
        $this->selectedSectionSizes = $selectedSectionSizes;
        $this->layoutCustomizerData = $layoutCustomizerData;
        $this->layoutProductData = $layoutProductData;
    }

    public function process(string $result): string
    {
        return '';
    }

    public function getDefaultValue()
    {
        /**
         * @var \Magento\Catalog\Model\Product\Option $option
         */
        $option = $this->getOption();
        $layoutDefaultData = $this->getDefaultLayoutData($option->getProduct());
        switch ($option->getGroupOptionId()) {
            case $this->layoutCustomizerData->getOverallWidthParamId():
                $width = $this->selectedWidth->get();
                if ($width == 0) {
                    $width = $layoutDefaultData['width'];
                }
                return $width;
            case $this->layoutCustomizerData->getOverallHeightParamId():
                $height =  $this->selectedHeight->get();
                if ($height == 0) {
                    $height = $layoutDefaultData['height'];
                }
                return $height;
            case $this->layoutCustomizerData->getSectionSizesParamId():
                $sectionSizes = $this->selectedSectionSizes->get();
                if (!$sectionSizes) {
                    $sectionSizes = '';
                }
                return $sectionSizes;
            default:
                if ($this->isSelectedOption($option, $this->selectedOptions)) {
                    return $this->getSelectedValue($option, $this->selectedOptions);
                }
                return $this->getBlockParent()->getDefaultValue();
        }
    }
    protected function getDefaultLayoutData($product)
    {
        if ($this->layoutDefaultData ===  null) {
            $this->layoutDefaultData = $this->layoutProductData->getLayoutData(
                $product->getData(LayoutCustomizerHelper::PRODUCT_LAYOUT_ATTR),
                $this->_storeManager->getStore()->getId(),
            );
        }
        return $this->layoutDefaultData;
    }


    public function isHiddenOption() :bool
    {
        return (bool)$this->getOption()->getData('hidden');
    }
}
