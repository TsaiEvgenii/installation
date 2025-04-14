<?php

namespace BelVG\LayoutCustomizer\Block\Product\View;

use BelVG\SaleCountdown\Api\Locator\GetActualRuleInterface;

/**
 * Class LayoutConfig
 * @package BelVG\LayoutCustomizer\Block\Product\View
 */
class LayoutConfig extends \Magento\Framework\View\Element\Template
{
    protected $_product;

    /** @var \BelVG\LayoutCustomizer\Model\LayoutRepository */
    public $layoutRepository;

    public $helper;

    public $registry;

    public $layoutBuilder;

    public $storeManager;

    public $jsonHelper;

    public $getActualRuleService;

    public function __construct(
        \BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface $layoutRepository,
        \BelVG\LayoutCustomizer\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \BelVG\LayoutCustomizer\Model\Config\LayoutBuilder $layoutBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        GetActualRuleInterface $getActualRuleService,
        array $data = []
    )
    {
        $this->layoutRepository = $layoutRepository;
        $this->helper = $helper;
        $this->registry = $registry;
        $this->layoutBuilder = $layoutBuilder;
        $this->storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->getActualRuleService = $getActualRuleService;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if ($this->registry->registry('current_product')) {
                $this->_product = $this->registry->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }

        return $this->_product;
    }

    /**
     * @param \Magento\Catalog\Model\Product|null $product
     * @return $this
     */
    public function setProduct(\Magento\Catalog\Model\Product $product = null)
    {
        $this->_product = $product;

        return $this;
    }

    public function getLayoutId()
    {
        return $this->getProduct()->getData(\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR);
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get actual SalesRule
     *
     * @return \BelVG\SaleCountdown\Api\Data\CountdownInterface|null
     */
    protected function getCurrentSaleRule()
    {
        $sale = $this->getActualRuleService->getActualRule();
        if ($sale !== NULL) {
            return $sale;
        }

        return null;
    }

    /**
     * Get actual SalesRule discount amount
     *
     * @return int
     */
    protected function getPercentOfCurrentSale()
    {
        return $this->getCurrentSaleRule() ? $this->getCurrentSaleRule()->getRuleDiscountAmount() : 0;
    }

    /**
     * Get actual SalesRule ID
     *
     * @return string|null
     */
    protected function getCurrentSaleRuleId()
    {
        return $this->getCurrentSaleRule() ? $this->getCurrentSaleRule()->getRuleId() : null;
    }

    /**
     * Return JSON with layout configuration
     */
    public function getJsonLayoutConfig()
    {
        $layout_id = $this->getProduct()->getData(\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR);
        //$layoutObj = $this->layoutRepository->getById($layout_id);

        $data = [
            'overall_width' => $this->helper->getOverallWidthOption(),
            'overall_height' => $this->helper->getOverallHeightOption(),
            'sections_sizes' => $this->helper->getSectionsSizesOption(),
            'layout_props' => $this->layoutBuilder->getLayoutProps($layout_id, $this->getStoreId()),
            'sale_percent' => $this->getPercentOfCurrentSale(),
            'sale_rule_id' => $this->getCurrentSaleRuleId(),
            'additional_default_colors' => $this->jsonHelper->serialize($this->helper->getAdditionalDefaultColors($this->getStoreId()))
        ];

        return $this->jsonHelper->serialize($data);
    }
}
