<?php


namespace BelVG\LayoutCustomizer\Model\Helper;

use BelVG\LayoutCustomizer\Api\Helper\QuoteItemOptionManagement as QuoteItemOptionManagementInterface;
use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use Magento\Framework\App\ResourceConnection;

class QuoteItemOptionManagement implements QuoteItemOptionManagementInterface
{
    private $_connection;

    private array $dimensions = [];

    public function __construct(
        private LayoutCustomizerHelper $layoutCustomizerHelper,
        private ResourceConnection $_resource
    ) {
        $this->_connection = $this->_resource->getConnection();
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $mageworx_group_option_id
     * @return int
     */
    public function getOptionValueByMageworxId(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $mageworx_group_option_id
    ) {
        //Find `option_id` of Width/Height by `mageworx_group_option_id`
        $sql = $this->_connection->select()
            ->from(['prod_option' => $this->_resource->getTableName('catalog_product_option')], ['option_id'])
            ->where('prod_option.`product_id` = ?', $quoteItem->getProductId()) //$quoteItem->getProduct()->getId()
            ->where('prod_option.`mageworx_group_option_id` = ?', $mageworx_group_option_id);
        $option_id = (int)$this->_connection->fetchOne($sql);

        return $option_id;
    }

    /**
     * @param int $quoteItemId
     * @return iterable|null
     */
    public function getOptions(
        int $quoteItemId
    ) :?iterable {
        $sql = $this->_connection->select()
            ->from($this->_resource->getTableName('quote_item_option'))
            ->where('item_id = ?', $quoteItemId);
        return $this->_connection->fetchAll($sql);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return int
     */
    public function getQuoteItemWidth(
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        $mageworx_group_option_id = $this->layoutCustomizerHelper->getOverallWidthOption();

        return $this->getOptionValueByMageworxId($quoteItem, $mageworx_group_option_id);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return int
     */
    public function getQuoteItemHeight(
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        $mageworx_group_option_id = $this->layoutCustomizerHelper->getOverallHeightOption();

        return $this->getOptionValueByMageworxId($quoteItem, $mageworx_group_option_id);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $option_id
     * @return mixed
     */
    protected function getOptionValueFromOption(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $option_id
    ) {
        foreach ($quoteItem->getOptions() as $custom_option) {
            if ($custom_option->getData('code') == self::OPT_PREFIX . $option_id) {
                return $custom_option->getData('value');
            }
        }
        unset($custom_option);

        return null;
    }

    /**
     * Mainly method should be used: \BelVG\LayoutCustomizer\Model\Helper\QuoteItemOptionManagement::getOptionValueFromOption
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $option_id
     * @return int
     */
    protected function getOptionValueFromDB(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $option_id
    ) {
        $sql = $this->_connection->select()
            ->from(['quote_item_option' => $this->_resource->getTableName('quote_item_option')], ['value'])
            ->where('quote_item_option.`code` = ?', 'option_' . $option_id)
            ->where('quote_item_option.`item_id` = ?', $quoteItem->getItemId());
        return (int)$this->_connection->fetchOne($sql);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $option_id
     * @return int|mixed
     */
    public function getProductOptionValue(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $option_id
    ) {
        if ($quoteItem->getOptions()) {
            return $this->getOptionValueFromOption($quoteItem, $option_id);
        } else {
            return $this->getOptionValueFromDB($quoteItem, $option_id);
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $forceLoad
     * @return array|mixed
     */
    public function getDimensions(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $forceLoad = false
    ) {
        $quoteItemId = $quoteItem->getItemId();
        $dimensions = !empty($this->dimensions[$quoteItemId]) ? $this->dimensions[$quoteItemId] : null;

        if (!$dimensions || $forceLoad) {
            $width_option_id = $this->getQuoteItemWidth($quoteItem);
            $height_option_id = $this->getQuoteItemHeight($quoteItem);

            $width = $this->getProductOptionValue($quoteItem, $width_option_id);
            $height = $this->getProductOptionValue($quoteItem, $height_option_id);

            $dimensions = ['width' => $width, 'height' => $height];
        }

        if ($quoteItemId !== null) {
            $this->dimensions[$quoteItemId] = $dimensions;
        }

        return $dimensions;
    }
}
