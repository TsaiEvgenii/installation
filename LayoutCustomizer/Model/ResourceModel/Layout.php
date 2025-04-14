<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel;

use BelVG\LayoutCustomizer\Helper\Layout\StoreData as StoreDataHelper;
use BelVG\LayoutCustomizer\Helper\Layout\Block as BlockHelper;
use BelVG\LayoutCustomizer\Api\Service\StoreFieldsInterface as CustomizerStoreFields;
use BelVG\LayoutCustomizer\Model\Config\PriceFieldsConfig;
use BelVG\LayoutCustomizer\Model\Service\TaxRateByStoreService;
use BelVG\LayoutCustomizer\Model\PriceCurrency as LayoutPriceCurrency;
use Magento\Tax\Model\Calculation;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;

class Layout extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const CM_IN_SQM = 10000;
    const CACHE_KEY_SEPARATOR = '_';

    protected $storeDataHelper;
    protected $blockHelper;
    protected $calculator;
    protected $storeFieldsService;
    protected $taxRateByStoreService;

    private $cachedMeasurement = [];
    private $cachedLayoutData = [];
    private $priceFieldsConfig;
    private $priceCurrency;

    /**
     * Layout constructor.
     * @param StoreDataHelper $storeDataHelper
     * @param BlockHelper $blockHelper
     * @param Calculation $calculator
     * @param CustomizerStoreFields $storeFieldsService
     * @param TaxRateByStoreService $taxRateByStoreService
     * @param DbContext $context
     * @param PriceFieldsConfig $priceFieldsConfig
     * @param LayoutPriceCurrency $priceCurrency
     * @param null $connectionName
     */
    public function __construct(
        StoreDataHelper $storeDataHelper,
        BlockHelper $blockHelper,
        Calculation $calculator,
        CustomizerStoreFields $storeFieldsService,
        TaxRateByStoreService $taxRateByStoreService,
        DbContext $context,
        PriceFieldsConfig $priceFieldsConfig,
        LayoutPriceCurrency $priceCurrency,
        $connectionName = null
    ) {
        $this->storeDataHelper = $storeDataHelper;
        $this->blockHelper = $blockHelper;
        $this->calculator = $calculator;
        $this->storeFieldsService = $storeFieldsService;
        $this->taxRateByStoreService = $taxRateByStoreService;
        $this->priceFieldsConfig = $priceFieldsConfig;
        $this->priceCurrency = $priceCurrency;

        parent::__construct($context, $connectionName);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('belvg_layoutcustomizer_layout', 'layout_id');
    }

    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        $this->loadStoreData($object);
        return $this;
    }

    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->saveStoreData($object);
        return $this;
    }

    public function saveStoreData($object)
    {
        $this->storeDataHelper->save(
            $object->getId(),
            $object->getStoreId(),
            $object->getData());
    }

    public function loadStoreData($object, $loadDefault = true)
    {
        $data = $this->storeDataHelper->load($object->getId(), $object->getStoreId());
        $object->addData($data);
    }

    public function saveBlockData($object, $data)
    {
        $this->blockHelper->save($object->getId(), (array) $data);
    }

    /**
     * Method returns layout details together with price depending on dimensions
     *
     * In case of change please sync logic with `app/code/BelVG/LayoutCustomizer/view/frontend/web/js/catalog/product/base.js`
     *
     * @param $layout_id
     * @param int $store_id
     * @param int $width
     * @param int $height
     * @return array
     */
    public function getLayoutData($layout_id, $store_id = 0, $width = 0, $height = 0)
    {
        $cacheKey = implode(self::CACHE_KEY_SEPARATOR, func_get_args());
        if (!isset($this->cachedLayoutData[$cacheKey])) {
            $connection = $this->getConnection();

            $_width_row = $width == 0 ? $this->getTotalMeasurement($layout_id, 'width') : (float)$width;
            $_height_row = $height == 0 ? $this->getTotalMeasurement($layout_id, 'height') : (float)$height;
            $_tax_rate = $this->taxRateByStoreService->getTaxRateMultiplier($store_id);

            $sql = $connection->select()
                ->from(['main_table' => $this->_mainTable])
                ->columns(
                    array_merge(
                        [
                            'width' => new \Zend_Db_Expr($_width_row),
                            'height' => new \Zend_Db_Expr($_height_row),
                            'sqm_price_step1' => new \Zend_Db_Expr(sprintf('IF(layoutstore.sqm_price is NOT NULL, layoutstore.sqm_price, defaultstore.sqm_price) * %1$f', $_tax_rate)),
                            'sqm_price_step2' => new \Zend_Db_Expr(sprintf('IF(IF(layoutstore.sqm_level_step2 is NOT NULL, layoutstore.sqm_level_step2, defaultstore.sqm_level_step2) > 0, ((IF(layoutstore.sqm_price is NOT NULL, layoutstore.sqm_price, defaultstore.sqm_price)) + (IF(layoutstore.sqm_price_step2 is NOT NULL, layoutstore.sqm_price_step2, defaultstore.sqm_price_step2))), (IF(layoutstore.sqm_price is NOT NULL, layoutstore.sqm_price, defaultstore.sqm_price))) * %1$f', $_tax_rate)),
                            'total_price' => new \Zend_Db_Expr(sprintf($_width_row . sprintf('*(IF(layoutstore.horizontal_frame is NOT NULL, layoutstore.horizontal_frame * %1$f, defaultstore.horizontal_frame * %1$f))+', $_tax_rate).$_height_row.sprintf('*(IF(layoutstore.vertical_frame is NOT NULL, layoutstore.vertical_frame * %1$f, defaultstore.vertical_frame * %1$f))', $_tax_rate) . '+(IF(layoutstore.base_price is NOT NULL, layoutstore.base_price, defaultstore.base_price) * %1$f)+IF((IF(layoutstore.sqm_level_step2 is NOT NULL, layoutstore.sqm_level_step2, defaultstore.sqm_level_step2)) > 0 && ('.$_height_row.' * '.$_width_row . '/' . (int)self::CM_IN_SQM.') > IF(layoutstore.sqm_level_step2 is NOT NULL, layoutstore.sqm_level_step2, defaultstore.sqm_level_step2), (IF(IF(layoutstore.sqm_level_step2 is NOT NULL, layoutstore.sqm_level_step2, defaultstore.sqm_level_step2) > 0, ((IF(layoutstore.sqm_price is NOT NULL, layoutstore.sqm_price, defaultstore.sqm_price) * %2$f) + (IF(layoutstore.sqm_price_step2 is NOT NULL, layoutstore.sqm_price_step2, defaultstore.sqm_price_step2)* %3$f)), (IF(layoutstore.sqm_price is NOT NULL, layoutstore.sqm_price, defaultstore.sqm_price)* %4$f))), (IF(layoutstore.sqm_price is NOT NULL, layoutstore.sqm_price, defaultstore.sqm_price) * %5$f))*total_sqm.total_sqm', $_tax_rate, $_tax_rate, $_tax_rate, $_tax_rate, $_tax_rate))
                        ],
                        $this->storeFieldsService->getZendDbExprForAll($store_id)
                    ))
                ->join([
                    'total_sqm' => $connection->select()
                        ->from(['main_table' => $this->_mainTable], 'layout_id')
                        ->columns([
                            'total_sqm' => new \Zend_Db_Expr(($_width_row . '*' . $_height_row . '/' . (int)self::CM_IN_SQM)),
                        ])
                ],
                    'main_table.layout_id = total_sqm.layout_id',
                    ['total_sqm']
                )
                ->joinLeft(['layoutstore' => $this->_resources->getTableName('belvg_layoutcustomizer_layoutstore')], 'main_table.layout_id = layoutstore.layout_id' . (!empty($store_id) ? ' and layoutstore.store_id = '.(int)$store_id : ''), [])
                ->joinLeft(['defaultstore' => $this->_resources->getTableName('belvg_layoutcustomizer_layoutstore')], 'main_table.layout_id = defaultstore.layout_id and defaultstore.store_id = 0', [])
                ->where('main_table.`layout_id` = ?', (int)$layout_id);
            $layoutData = $connection->fetchRow($sql);

            if (is_array($layoutData)) {
                $this->convertPrices($layoutData, $store_id);
            }

            $this->cachedLayoutData[$cacheKey] = $layoutData;
        }

        return $this->cachedLayoutData[$cacheKey];
    }

    /**
     * @return void
     */
    public function cleanCache() :void {
        $this->cachedLayoutData = [];
        $this->cachedMeasurement = [];
    }

    /**
     * @param array $layoutData
     * @param $storeId
     */
    protected function convertPrices(array &$layoutData, $storeId)
    {
        $fields = array_intersect(array_keys($layoutData), $this->priceFieldsConfig->getFields());
        foreach ($fields as $field) {
            $layoutData[$field] = $this->priceCurrency->convert($layoutData[$field], $storeId);
        }
    }

    /**
     * Get actual width/height based on measurement and adjustment values
     *
     * @param $layout_id
     * @param string $type
     * @return float
     */
    protected function getTotalMeasurement(
        $layout_id,
        string $type
    ) {
        $cacheKey = implode(self::CACHE_KEY_SEPARATOR, func_get_args());
        if (!isset($this->cachedMeasurement[$cacheKey])) {
            $connection = $this->getConnection();
            $sql = $connection->select()
                ->from(
                    [
                        'layout_block' => $this->_resources->getTableName('belvg_layoutcustomizer_layout_block')
                    ],
                    []
                )
//            ->columns([
//                'block_id',
//                'layout_id',
//                $type,
//            ])
                ->join(
                    ['layout_measurement' => $this->_resources->getTableName('belvg_layoutcustomizer_layout_measurement')],
                    'layout_measurement.block_id = layout_block.block_id AND layout_measurement.type = "' . $type . '"',
//                ['type'],
                    []
                )
                ->join([
                    'measurement_param' => $connection->select()
                        ->from(
                            [
                                'layout_measurement_param' => $this->_resources->getTableName('belvg_layoutcustomizer_layout_measurement_param')
                            ],
                            []
                        )
                        ->columns([
                            'adjustment_sum' => new \Zend_Db_Expr('SUM(layout_measurement_param.value)'),
                            'measurement_id'
                        ])
                        ->where('layout_measurement_param.name IN (\'adjustment1\', \'adjustment2\')')
                        ->group('layout_measurement_param.measurement_id')
                ],
                    'measurement_param.measurement_id = layout_measurement.measurement_id',
//                [
//                    'adjustment_sum',
//                    'measurement_id',
//                    'total_' . $type => new \Zend_Db_Expr('measurement_param.adjustment_sum + layout_block.' . $type)
//                ],
                    ['total_' . $type => new \Zend_Db_Expr('measurement_param.adjustment_sum + layout_block.' . $type)]
                )
                ->where('layout_block.`layout_id` = ?', (int)$layout_id);

            $this->cachedMeasurement[$cacheKey] = (float)$connection->fetchOne($sql);
        }

        return $this->cachedMeasurement[$cacheKey];
    }
}
