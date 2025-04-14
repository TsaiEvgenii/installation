<?php


namespace BelVG\LayoutCustomizer\Override\MageWorx\OptionTemplates\Model\OptionSaver;



class ValueOverride extends \MageWorx\OptionTemplates\Model\OptionSaver\Value
{
    public $pubBaseHelper;

    public $pubOptionValueAttributes;

    public $layoutCustomizerHelper;

    public $isValueNew;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \MageWorx\OptionBase\Model\Product\Option\Value\Attributes $optionValueAttributes,
        \MageWorx\OptionBase\Helper\Data $baseHelper,
        \BelVG\LayoutCustomizer\Helper\Data $layoutCustomizerHelper,
        $connectionName = null
    ) {
        $this->pubBaseHelper = $baseHelper;
        $this->pubOptionValueAttributes = $optionValueAttributes;
        $this->layoutCustomizerHelper = $layoutCustomizerHelper;

        parent::__construct($context, $currencyFactory, $storeManager, $config, $optionValueAttributes, $baseHelper, $connectionName);
    }

    /**
     * Override reason: make function public
     *
     * Collect option prices for option values before multiple insert
     *
     * @param \Magento\Catalog\Model\Product\Option\Value $value
     * @param array $optionData
     * @return void
     */
    public function collectPriceDataPub($value, &$optionData)
    {
        $priceTable = $this->getTable(self::TABLE_NAME_CATALOG_PRODUCT_OPTION_TYPE_PRICE);

        $price = (double)sprintf('%F', $value['price']);
        $priceType = $value['price_type'];

        if ($value['price'] && $priceType) {
            $data = $this->_prepareDataForTable(
                new \Magento\Framework\DataObject(
                    [
                        'option_type_id' => (int)$value['option_type_id'],
                        'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        'price' => $price,
                        'price_type' => $priceType,
                    ]
                ),
                $priceTable
            );
            $optionData[self::TABLE_NAME_CATALOG_PRODUCT_OPTION_TYPE_PRICE][$value['option_type_id']] = $this->_prepareDataForTable(
                new \Magento\Framework\DataObject($data),
                $priceTable
            );
        }
    }

    /**
     * Override reason: make function public
     *
     * Collect option prices for option titles before multiple insert
     *
     * @param \Magento\Catalog\Model\Product\Option\Value $value
     * @param array $optionData
     * @return void
     */
    protected function collectTitleDataPub($value, &$optionData)
    {
        $titleTableName = $this->getTable(self::TABLE_NAME_CATALOG_PRODUCT_OPTION_TYPE_TITLE);
        $storeIds = [];
        if ($this->isValueNew){
            $storeIds[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            $storeIds[] = $this->_storeManager->getStore()->getId();
        } else {
            $storeIds[] = $this->_storeManager->getStore()->getId();
        }
        foreach ($storeIds as $storeId) {
            if (!$value['title']) {
                return;
            }

            $data = $this->_prepareDataForTable(
                new \Magento\Framework\DataObject(
                    [
                        'option_type_id' => $value['option_type_id'],
                        'store_id' => $storeId,
                        'title' => $value['title'],
                    ]
                ),
                $titleTableName
            );

            $optionData[self::TABLE_NAME_CATALOG_PRODUCT_OPTION_TYPE_TITLE][] =
                $this->_prepareDataForTable(
                    new \Magento\Framework\DataObject($data),
                    $titleTableName
                );
        }
    }

    /**
     * Override reason `mageworx_optiontemplates_group_option_type_id` column
     *
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option
     * @param array $value
     * @param array $optionData
     * @param array $currentIncrementIds
     * @return array
     */
    protected function collectValueData($option, $value, &$optionData, &$currentIncrementIds)
    {
        $newOptionValueId = $currentIncrementIds['value'];
        $currentIncrementIds['value'] += 1;

        //        $value['option_type_id'] = $newOptionValueId;
        if (!$value['option_type_id']) {
            $value['option_type_id'] = $newOptionValueId; //new option value [override reason: MageWorx save issue]
        }

        $value['option_id'] = $option->getData('option_id');
        $this->isValueNew = false;
        if (empty($value['mageworx_option_type_id'])) {
            $this->isValueNew = true;
            $value['mageworx_option_type_id'] = $this->pubBaseHelper->generateUUIDv4();
        }
        $value['group_option_id'] = $option->getData('group_option_id');
        $value['mageworx_option_id'] = $option->getData('mageworx_option_id');

        $data = [
            'option_type_id' => $value['option_type_id'],
            'option_id' => $option->getData('option_id'),
            'mageworx_option_type_id' => $value['mageworx_option_type_id'],
            'mageworx_optiontemplates_group_option_type_id' => $this->layoutCustomizerHelper->getMageWorxOptionTypeIdByOptionTypeId($value['group_option_value_id']), //$value['mageworx_group_option_type_id'], //override reason
            'group_option_value_id' => $value['group_option_value_id'],
            'sku' => $value['sku'],
            'sort_order' => $value['sort_order']
        ];

        foreach ($this->pubOptionValueAttributes->getData() as $attribute) {
            if (!$attribute->hasOwnTable()) {
                if (!isset($value[$attribute->getName()])) {
                    $data[$attribute->getName()] = null;
                } else {
                    $data[$attribute->getName()] = $attribute->prepareDataBeforeSave($value);
                }
            }
        }

        $catalogProductOptionTable = $this->getTable(self::TABLE_NAME_CATALOG_PRODUCT_OPTION_TYPE_VALUE);
        $optionData[self::TABLE_NAME_CATALOG_PRODUCT_OPTION_TYPE_VALUE][$value['option_type_id']] =
            $this->_prepareDataForTable(
                new \Magento\Framework\DataObject($data),
                $catalogProductOptionTable
            );

        $this->collectPriceDataPub($value, $optionData);
        $this->collectTitleDataPub($value, $optionData);

        return $value;
    }
}
