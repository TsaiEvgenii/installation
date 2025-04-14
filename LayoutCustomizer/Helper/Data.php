<?php


namespace BelVG\LayoutCustomizer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionFeatures\Model\Product\Option\Value\Media\Config as MWOTFeaturesMediaConfig;

class Data extends AbstractHelper
{
    protected $_connection;

    protected $widthParamId;
    protected $heightParamId;
    protected $sectionSizesParamId;

    const PRODUCT_LAYOUT_ATTR = 'belvg_layout';
    const PRODUCT_HEIGHT_OPTION_KEY = 'height';
    const PRODUCT_WIDTH_OPTION_KEY = 'width';

    const OVERALL_WIDTH_CONFIG_PATH = 'layout_customizer/general/overall_width_attribute';
    const OVERALL_HEIGHT_CONFIG_PATH = 'layout_customizer/general/overall_height_attribute';
    const SECTIONS_SIZES_CONFIG_PATH = 'layout_customizer/general/sections_sizes_attribute';
    const ADDITIONAL_DEFAULT_COLORS_PATH = 'layout_customizer/general/additional_default_colors';
    const OPTION_DESCRIPTION_LINK = 'layout_customizer/general/option_description_link';

    const DEFAULT_STORE_ID = 0;

    /**
     * @var string[]
     */
    protected array $dbOptions = [];

    public function __construct(
        HelperContext $context,
        private ResourceConnection $_resource,
        private MWOTFeaturesMediaConfig $mwotFeaturesMediaConfig
    ) {
        $this->_connection = $this->_resource->getConnection();

        parent::__construct($context);
    }

    public function getConfig($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getOverallWidthOption($store = null)
    {
        return $this->getConfig(self::OVERALL_WIDTH_CONFIG_PATH, $store);
    }

    public function getOverallHeightOption($store = null)
    {
        return $this->getConfig(self::OVERALL_HEIGHT_CONFIG_PATH, $store);
    }

    public function getOverallWidthParamId()
    {
        if (is_null($this->widthParamId)) {
            $this->loadOverallDimensionParamIds();
        }
        return $this->widthParamId ?: null;
    }

    public function getOverallHeightParamId()
    {
        if (is_null($this->heightParamId)) {
            $this->loadOverallDimensionParamIds();
        }
        return $this->heightParamId ?: null;
    }

    public function getSectionSizesParamId()
    {
        if (is_null($this->sectionSizesParamId)) {
            $this->loadOverallDimensionParamIds();
        }
        return $this->sectionSizesParamId ?: null;
    }

    public function getSectionsSizesOption($store = null)
    {
        return $this->getConfig(self::SECTIONS_SIZES_CONFIG_PATH, $store);
    }

    public function getAdditionalDefaultColors($store = null)
    {
        return $this->getConfig(self::ADDITIONAL_DEFAULT_COLORS_PATH, $store);
    }

    public function getOptionDescriptionLink($store = null)
    {
        return $this->getConfig(self::OPTION_DESCRIPTION_LINK, $store);
    }

    public function getMageWorxOptionTypeIdByOptionTypeId($option_type_id)
    {
        $sql = $this->_connection->select()
            ->from(['val' => $this->_resource->getTableName('mageworx_optiontemplates_group_option_type_value')], ['mageworx_option_type_id'])
            ->join(
                [
                    'title' => $this->_resource->getTableName('mageworx_optiontemplates_group_option_type_title')
                ],
                'val.option_type_id = title.option_type_id'
            )
            ->where('val.`option_type_id` = ?', (int)$option_type_id);
        return $this->_connection->fetchOne($sql);
    }

    /**
     * @param $optionId
     * @param $configValue
     * @param $mwotOptionValueHash
     * @param $forceLoad
     * @return bool
     */
    public function matchDbOptionWithConfig(
        $optionId,
        $configValue,
        $mwotOptionValueHash = '',
        $forceLoad = false
    ) {
        $stopList = ['info_buyRequest', 'ids'];
        if (in_array($optionId, $stopList)) {
            return false;
        }

        $option = $this->loadDbOption($optionId, $mwotOptionValueHash, $forceLoad);

        return isset($option['mageworx_group_option_id']) && $option['mageworx_group_option_id'] == $configValue;
    }

    /**
     * @param $optionId
     * @param $mwotOptionValueHash
     * @return string|null
     */
    public function getMageWorxOptionImg(
        $optionId,
        $mwotOptionValueHash
    ) :?string {
        $option = $this->loadDbOption($optionId, $mwotOptionValueHash);

        return ( array_key_exists('mwot_image', $option) && $option['mwot_image']) ? $this->mwotFeaturesMediaConfig->getMediaUrl($option['mwot_image']) : null;
    }

    /**
     * @param string $sku
     * @return mixed
     */
    public function getLayoutIdBySku(string $sku) {
        $sql = $this->_connection->select()
            ->from(
                [
                    'layout' => $this->_resource->getTableName('belvg_layoutcustomizer_layout')
                ],
                [
                    'layout_id',
                ]
            )
            ->where('layout.`identifier` = ?', $sku);

        return $this->_connection->fetchOne($sql);
    }

    protected function getDbOption(
        $optionId,
        $mwotOptionValueHash
    ) {
        $sql = $this->_connection->select()
            ->from(
                [
                    'prod_option' => $this->_resource->getTableName('catalog_product_option')
                ],
                [
                    'option_id',
                    'product_id',
                    'mageworx_option_id',
                    'mageworx_group_option_id',
                ]
            )
            ->join(
                [
                    'mageworx_group_option' => $this->_resource->getTableName('mageworx_optiontemplates_group_option')
                ],
                'mageworx_group_option.option_id = prod_option.group_option_id',
                [
                    'option_id',
                ]
            )
            ->joinLeft(
                [
                    'gr_option_value' => $this->_resource->getTableName('mageworx_optiontemplates_group_option_type_value')
                ],
                'mageworx_group_option.option_id = gr_option_value.option_id AND gr_option_value.mageworx_option_type_id = ' . $this->_connection->quote($mwotOptionValueHash),
                [
                    'mageworx_option_type_id',
                ]
            )
            ->joinLeft(
                [
                    'gr_option_image' => $this->_resource->getTableName('mageworx_optiontemplates_group_option_type_image')
                ],
                'gr_option_value.mageworx_option_type_id = gr_option_image.mageworx_option_type_id',
                [
                    'mwot_media_type' => 'gr_option_image.media_type',
                    'mwot_image' => 'gr_option_image.value',
                ]
            )
            ->joinLeft(
                [
                    'mageworx_group_option_title' => $this->_resource->getTableName('mageworx_optiontemplates_group_option_title')
                ],
                'mageworx_group_option_title.option_id = mageworx_group_option.option_id AND mageworx_group_option_title.store_id = ' . (int)$this->getDefaultStoreId(),
                [
                    'title',
                ]
            )
            ->where('prod_option.`option_id` = ?', (int) $optionId);

        return $this->_connection->fetchRow($sql) ?: [];
    }

    protected function getDefaultStoreId() {
        return self::DEFAULT_STORE_ID;
    }

    protected function loadOverallDimensionParamIds()
    {
        $widthUuid  = $this->getOverallWidthOption();
        $heightUuid = $this->getOverallHeightOption();
        $sectionSizesUuid = $this->getSectionsSizesOption();

        $this->widthParamId  = false;
        $this->heightParamId = false;
        $this->sectionSizesParamId = false;

        if ($widthUuid || $heightUuid || $sectionSizesUuid) {
            $optionTable = $this->_resource->getTableName('mageworx_optiontemplates_group_option');
            $select = $this->_connection
                ->select()
                ->from(
                    ['options' => $optionTable],
                    ['mageworx_option_id', 'option_id'])
                ->where('mageworx_option_id IN (?)', array_filter([$widthUuid, $heightUuid, $sectionSizesUuid]));
            $optionMap = $this->_connection->fetchPairs($select);
            foreach ($optionMap as $mwOptionId => $optionId) {
                if ($mwOptionId == $widthUuid) {
                    $this->widthParamId = $optionId;
                } elseif ($mwOptionId == $heightUuid) {
                    $this->heightParamId = $optionId;
                } elseif ($mwOptionId == $sectionSizesUuid) {
                    $this->sectionSizesParamId = $optionId;
                }
            }
        }
    }

    private function loadDbOption(
        $optionId,
        $mwotOptionValueHash,
        $forceLoad = false
    ) {
        $hashKey = implode('__', [$optionId, $mwotOptionValueHash]);
        if (!isset($this->dbOptions[$hashKey]) || $forceLoad) {
            $option = $this->getDbOption($hashKey, $mwotOptionValueHash);
            $this->dbOptions[$hashKey] = $option;
        } else {
            $option = $this->dbOptions[$hashKey];
        }

        return $option;
    }
}
