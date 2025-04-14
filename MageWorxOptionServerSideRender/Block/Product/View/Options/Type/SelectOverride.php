<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Product\View\Options\Type;

use BelVG\LayoutOptionPriceType\Model\Service\Renderer\AfterTitlePool;
use BelVG\LayoutOptionPriceType\Override\Magento\Catalog\Block\Product\View\Options\Type\SelectOverride as SelectOverrideParent;
use BelVG\LayoutOptionPriceType\Plugin\Magento\Catalog\Model\Config\Source\Product\Options\PricePlugin as BelVGLayoutOptionPriceTypePricePlugin;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedOptionProcessor;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\PriceDiscountInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\SelectedRequestOptionInterface;
use BelVG\SaleCountdown\Api\Locator\GetActualRuleInterface;
use Magento\Catalog\Block\Product\View\Options\Type\Select\CheckableFactory;
use Magento\Catalog\Block\Product\View\Options\Type\Select\MultipleFactory;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;

class SelectOverride extends SelectOverrideParent
{
    use SelectedOptionProcessor;
    protected SelectedRequestOptionInterface $selectedRequestOption;
    /**
     * @var \BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface[]|iterable
     */
    protected GetSelectedOptions $selectedOptions;
    private \BelVG\LayoutCustomizer\Helper\Data $layoutCustomizerData;
    private PriceDiscountInterface $priceDiscount;
    private PriceDiscountInterface $priceDiscountService;

    /**
     * SelectOverride constructor.
     * @param Context $context
     * @param Data $pricingHelper
     * @param CatalogHelper $catalogData
     * @param GetActualRuleInterface $getActualRuleService
     * @param CheckableFactory|null $checkableFactory
     * @param MultipleFactory|null $multipleFactory
     * @param LoggerInterface $logger
     * @param GetSelectedOptions $selectedOption
     * @param \BelVG\LayoutCustomizer\Helper\Data $layoutCustomizerData
     * @param PriceDiscountInterface $priceDiscountService
     * @param AfterTitlePool $afterTitlePool
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $pricingHelper,
        CatalogHelper $catalogData,
        LoggerInterface $logger,
        GetSelectedOptions $selectedOption,
        \BelVG\LayoutCustomizer\Helper\Data $layoutCustomizerData,
        PriceDiscountInterface $priceDiscountService,
        AfterTitlePool $afterTitlePool,
        array $data = [],
        CheckableFactory $checkableFactory = null,
        MultipleFactory $multipleFactory = null,
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $logger, $afterTitlePool, $data, $checkableFactory, $multipleFactory,);
        $this->selectedOptions = $selectedOption;
        $this->layoutCustomizerData = $layoutCustomizerData;
        $this->priceDiscountService = $priceDiscountService;
    }

    public function getValuesHtml() :string
    {
        $_option = $this->getOption();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $store = $this->getProduct()->getStore();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');

        $this->setSkipJsReloadPrice(1);
        // Remove inline prototype onclick and onchange events

        if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN ||
            $_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE
        ) {
 //            @see app/code/MageWorx/OptionBase/Plugin/ValidateAddToCart.php:58
//            after this validation our options became not required that lead us to unexpected behaviours
            $require = $_option->getOrigData('is_require') ? ' required' : '';
            $extraParams = '';
            $select = $this->getLayout()->createBlock(
                \Magento\Framework\View\Element\Html\Select::class
            )->setData(
                [
                    'id' => 'select_' . $_option->getId(),
                    'class' => $require . ' product-custom-option admin__control-select'
                ]
            );
            if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options[' . $_option->getId() . ']')->addOption('', __('-- Please Select --'));
            } else {
                $select->setName('options[' . $_option->getId() . '][]');
                $select->setClass('multiselect admin__control-multiselect' . $require . ' product-custom-option');
            }
            foreach ($_option->getValues() as $_value) {
                $priceStr = $this->_formatPrice(
                    [
                        'is_percent' => $_value->getPriceType() == 'percent',
                        'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                    ],
                    false
                );
                $select->addOption(
                    $_value->getOptionTypeId(),
                    $_value->getTitle() . ' ' . strip_tags($priceStr) . '',
                    ['price' => $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false)]
                );
            }
            if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            if (!$this->getSkipJsReloadPrice()) {
                $extraParams .= ' onchange="opConfig.reloadPrice()"';
            }
            $extraParams .= ' data-selector="' . $select->getName() . '"';
            $select->setExtraParams($extraParams);

            if ($configValue) {
                $select->setValue($configValue);
            }

            return $select->getHtml();
        }

        if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_RADIO ||
            $_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX
        ) {
            $selectHtml = '<div class="overflow-wrapper">';
            $selectHtml .= $_option->getDescription() ? '<p class="desc">' . $_option->getDescription() . '</p>' : '';
            $selectHtml .= '<div class="options-list nested" id="options-' . $_option->getId() . '-list">';

//            @see app/code/MageWorx/OptionBase/Plugin/ValidateAddToCart.php:58
//            after this validation our options became not required that lead us to unexpected behaviours
            $require = $_option->getOrigData('is_require') ? ' required' : '';
            $arraySign = '';
            $type = '';
            $class = '';
            switch ($_option->getType()) {
                case \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio admin__control-radio';
                    if (!$_option->getOrigData('is_require')) {
                        $selectHtml .= '<div class="field choice none admin__field admin__field-option">' .
                            '<input type="radio" id="options_' .
                            $_option->getId() .
                            '" class="' .
                            $class .
                            ' product-custom-option" name="options[' .
                            $_option->getId() .
                            ']"' .
                            ' data-selector="options[' . $_option->getId() . ']"' .
                            ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                            ' value="" checked="checked" />' .
                            '<label class="label admin__field-label" for="options_' . $_option->getId() . '">' .
                            '<p class="title-cont">' .
                            '<span class="title">' . __('None') . '</span>' .
                            '</p>' .
                            '</label>' .
                            '</div>';
                    }
                    break;
                case \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox admin__control-checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;

                $priceStr = $this->_formatPrice(
                    [
                        'is_percent' => $_value->getPriceType() == 'percent',
                        'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                    ]
                );

                /*override reason [begin]*/
                if (!empty($priceStr) && $_value->getPriceType() == BelVGLayoutOptionPriceTypePricePlugin::VALUE_SQM_PRICE) {
                    $priceStr = $this->addSqmPostfix($priceStr);
                }
                /*override reason [end]*/
                $htmlValue = $_value->getOptionTypeId();
                $dataSelector = 'options[' . $_option->getId() . ']';
                $valueKey = 'data-value-key="' . $_value->getOptionTypeKey() . '"';
                $active = '';
                $checked = '';
                if ($arraySign) {
                    $dataSelector .= '[' . $htmlValue . ']';
                }
                $is_default = ((int)$_value['is_default'] == 0) ? '' : ' default ';
                $value = $this->getSelectedValue($_option, $this->selectedOptions);
                if ($value->getId() === $_value->getId()) {
                    $active = 'active';
                    $checked = 'checked';
                }

                //hide option price type in case In/Out color is used
                $price_type = ' (' . $_value['price_type'] . ')';
                if ($_option->getInsideOutsideColor()) {
                    $price_type = '';
                }

                $selectHtml .= '<div class="field choice admin__field admin__field-option ' . $is_default . $active . ' options_' . $_option->getId() . ' ' .
                    $require .
                    '">' .
                    '<input type="' .
                    $type .
                    '" class="' .
                    $class .
                    ' ' .
                    $require .
                    ' product-custom-option"' .
                    ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                    ' name="options[' .
                    $_option->getId() .
                    ']' .
                    $arraySign .
                    '" id="options_' .
                    $_option->getId() .
                    '_' .
                    $count .
                    '" value="' .
                    $htmlValue .
                    '" ' .
                    $checked .
                    ' data-selector="' . $dataSelector . '"' .
                    $valueKey .
                    ' price="' .
                    $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false) .
                    '" tabindex="-1" />' .
                    '<label class="label admin__field-label" for="options_' . $_option->getId() . '_' . $count . '">' .
                    '<p class="title-cont">' .
                    '<span class="title">' . $_value->getTitle() . '</span>' .
                    $this->renderActualPriceWithDiscount($_value, $store) .
                    $this->renderOldPrice($_option, $_value, $store) .
                    ($_value->getPriceType() == BelVGLayoutOptionPriceTypePricePlugin::VALUE_SQM_PRICE ? $this->renderSqmPrice($_value, $store) : '') .
                    '</p>' .
                    $this->renderAfterTitle($_option, $_value, $store) .
                    '</label>';
                $selectHtml .= '</div>';
            }


            $_option->getWarning() ? $selectHtml .= '</div><p class="alert-msg">
                <span class="text">' . $_option->getWarning() . '</span></p>' : false;

            $selectHtml .= '</div>';

            return $selectHtml;
        }
        return '';
    }

    protected function renderActualPriceWithDiscount(
        \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface $value,
        \Magento\Store\Api\Data\StoreInterface $store
    ) {
        $price = $value->getPrice(true);
        $price = $this->priceDiscountService->modifier((float)$price, $value->getOption()->getProduct());

        $this->checkPrice($value, $price, 'renderActualPrice');

        return $this->renderOptionPrice((float)$price, $value, $store, ['current', 'price']);
    }
}
