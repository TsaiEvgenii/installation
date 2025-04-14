<?php
namespace BelVG\LayoutCustomizer\Model\Config;

use BelVG\LayoutCustomizer\Helper\Layout\Block as LayoutBlockHelper;
use BelVG\LayoutCustomizer\Model\PriceCurrency as LayoutPriceCurrency;

class LayoutBuilder
{
    protected $layoutBlockHelper;
    protected $layoutRepository;
    protected $storeFieldsService;
    protected $resource;
    protected $priceCurrency;
    /**
     * @var PriceFieldsConfig
     */
    private $priceFieldsConfig;

    /**
     * LayoutBuilder constructor.
     * @param LayoutBlockHelper $layoutBlockHelper
     * @param \BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface $layoutRepository
     * @param \BelVG\LayoutCustomizer\Api\Service\StoreFieldsInterface $storeFieldsService
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param LayoutPriceCurrency $priceCurrency
     * @param PriceFieldsConfig $priceFieldsConfig
     */
    public function __construct(
        LayoutBlockHelper $layoutBlockHelper,
        \BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface $layoutRepository,
        \BelVG\LayoutCustomizer\Api\Service\StoreFieldsInterface $storeFieldsService,
        \Magento\Framework\App\ResourceConnection $resource,
        LayoutPriceCurrency $priceCurrency,
        PriceFieldsConfig $priceFieldsConfig
    ) {
        $this->layoutBlockHelper = $layoutBlockHelper;
        $this->layoutRepository = $layoutRepository;
        $this->storeFieldsService = $storeFieldsService;
        $this->resource = $resource;
        $this->priceCurrency = $priceCurrency;
        $this->priceFieldsConfig = $priceFieldsConfig;
    }

    public function getLayoutProps($layoutId, $storeId)
    {
        $connection = $this->resource->getConnection();
        $sql = $connection->select()
            ->from(['layout' => $this->resource->getTableName('belvg_layoutcustomizer_layout')])
            ->columns(
                $this->storeFieldsService->getZendDbExprForAll($storeId)
            )
            ->joinLeft(
                ['layoutstore' => $this->resource->getTableName('belvg_layoutcustomizer_layoutstore')],
                $connection->quoteInto(
                    'layout.layout_id = layoutstore.layout_id and layoutstore.store_id = ?',
                    $storeId),
                ['layoutstore_id', 'store_id']
            )
            ->joinLeft(
                ['defaultstore' => $this->resource->getTableName('belvg_layoutcustomizer_layoutstore')],
                'layout.layout_id = defaultstore.layout_id and defaultstore.store_id = 0',
                []
            )
            ->where('layout.`layout_id` = ?', $layoutId);
        $layoutData = $connection->fetchRow($sql);

        if (!empty($layoutData['layout_id'])) {
            $layoutId = $layoutData['layout_id'];
            $layoutData['blocks'] = $this->layoutBlockHelper->load($layoutId);
        }

        $this->convertPrices($layoutData, $storeId);

        return $layoutData;
    }

    protected function convertPrices(array &$layoutData, $storeId)
    {
        $fields = array_intersect(array_keys($layoutData), $this->priceFieldsConfig->getFields());
        foreach ($fields as $field) {
            $layoutData[$field] = $this->priceCurrency->convert($layoutData[$field], $storeId);
        }
    }
}
