<?php
namespace BelVG\LayoutCustomizer\Helper\Layout;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;

class StoreData
{
    protected $resources;
    protected $connection;
    protected $storeFieldsService;

    // [field1, field2, ...]
    protected $fields = [];
    protected $table;

    public function __construct(
        DbContext $context,
        \BelVG\LayoutCustomizer\Api\Service\StoreFieldsInterface $storeFields
    ) {
        $this->resources = $context->getResources();
        $this->storeFieldsService = $storeFields;
        $this->fields = $this->storeFieldsService->getStoreSeparatedFields();
        $this->table = $this->resources->getTableName('belvg_layoutcustomizer_layoutstore');
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function save($layoutId, $storeId, array $data)
    {
        $data = array_intersect_key($data, array_fill_keys($this->fields, true));
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->table,
                array_merge($data, [
                    'layout_id' => $layoutId,
                    'store_id' => $storeId
                ]));
        }
    }

    public function load($layoutId, $storeId, $loadDefault = true)
    {
        $data = $this->getDefaultData();
        $storeIds = array_merge([$storeId], ($loadDefault ? [0] : []));
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->table, $this->fields)
            ->where('layout_id = ?', $layoutId)
            ->where('store_id IN (?)', $storeIds)
            ->order(['store_id ASC']);
        foreach ($connection->fetchAll($select) as $row) {
            $data = array_merge(
                $data,
                array_filter($row, function($value) {
                    return !is_null($value);
                }));
        }
        return $data;
    }

    public function join($collection, $storeId)
    {
        $select = $collection->getSelect();

        // Join default store
        $select->joinLeft(
            ['price_default' => $this->table],
            'price_default.layout_id = main_table.layout_id AND price_default.store_id = 0',
            []);
        if ($storeId != 0) {
            // Join current store
            $connection = $collection->getConnection();
            $select
                ->columns(
                    ['store_id' => 'price_store.store_id']
                )
                ->joinLeft(
                    ['price_store' => $this->table],
                    $connection->quoteInto(
                        'price_store.layout_id = main_table.layout_id AND price_store.store_id = ?',
                        $storeId),
                    []
                );
        }

        // Add columns
        $columns = array_combine(
            $this->fields,
            array_map(function($field) use ($storeId) {
                return $storeId == 0
                    ? sprintf('price_default.%s', $field)
                    : sprintf('IFNULL(price_store.%1$s, price_default.%1$s)', $field);
            }, $this->fields));
        $select->columns($columns);
    }

    protected function getDefaultData()
    {
        return array_fill_keys($this->fields, null);
    }

    protected function getConnection()
    {
        return $this->resources->getConnection(ResourceConnection::DEFAULT_CONNECTION);
    }
}
