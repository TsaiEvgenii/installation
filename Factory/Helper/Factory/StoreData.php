<?php
namespace BelVG\Factory\Helper\Factory;

use BelVG\Factory\Model\Factory;
use BelVG\Factory\Model\ResourceModel\Factory as FactoryResource;
use BelVG\Factory\Model\ResourceModel\Factory\Collection;

class StoreData
{
    protected $resource;
    protected $fields;
    protected $customFields;

    public function __construct(FactoryResource\Proxy $resource, $fields = [], $customFields = [])
    {
        $this->resource = $resource;
        $this->fields = $fields;
        $this->customFields = $customFields;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getCustomFields()
    {
        return $this->customFields;
    }

    public function getTable()
    {
        return $this->resource->getTable('belvg_factory_store');
    }

    public function save(Factory $factory)
    {
        if (!$factory->getId())
            return;

        // Get store data from model
        $data = array_intersect_key(
            $factory->getData(),
            array_fill_keys($this->fields, true));

        $storeId = $factory->getStoreId();
        $connection = $this->resource->getConnection();

        if (!empty($data)) {
            // Save for current store
            $connection->insertOnDuplicate(
                $this->getTable(),
                array_merge($data, [
                    'factory_id' => $factory->getId(),
                    'store_id'   => $storeId
                ]));

            if ($storeId != 0) {
                // Save values for default store (if unset)
                $defaultData = $this->loadById($factory->getId(), 0, false);
                $isDefaultDataChanged = false;
                foreach ($data as $field => $value) {
                    if (is_null($defaultData[$field] ?? null)) {
                        $defaultData[$field] = $value;
                        $isDefaultDataChanged = true;
                    }
                }
                if ($isDefaultDataChanged) {
                    $connection->insertOnDuplicate(
                        $this->getTable(),
                        array_merge($defaultData, [
                            'factory_id' => $factory->getId(),
                            'store_id'   => 0
                        ]));
                }
            }
        }
    }

    public function loadById($factoryId, $storeId, $loadDefault = true) {
        if (!$factoryId)
            return [];

        $connection = $this->resource->getConnection();
        $storeIds = array_merge([$storeId], ($loadDefault ? [0] : []));
        $select = $connection->select()
            ->from(
                $this->getTable(),
                array_merge($this->fields, ['store_id']))
            ->where('factory_id = ?', $factoryId)
            ->where('store_id IN (?)', $storeIds)
            ->order(['store_id ASC']);
        $data = $this->getDefaultData();
        foreach ($connection->fetchAll($select) as $row) {
            foreach ($this->fields as $field) {
                if (isset($row[$field]) && !is_null($row[$field])) {
                    // Set field
                    $data[$field] = $row[$field];
                    // Set _store field
                    if (isset($row['store_id']) && $row['store_id'] != 0) {
                        $data[$field . '_store'] = $row[$field];
                    }
                }
            }
        }

        return $data;
    }

    public function load(Factory $factory, $loadDefault = true)
    {
        if (!$factory->getId())
            return [];

        $storeData = $this->loadById(
            $factory->getId(),
            $factory->getStoreId(),
            $loadDefault);
        $factory->addData($storeData);
    }

    public function join(Collection $collection)
    {
        $select = $collection->getSelect();
        $storeId = $collection->getStoreId();

        // Join default store data
        $select->joinLeft(
            ['default_store_data' => $this->getTable()],
            implode(' AND ', [
                'default_store_data.factory_id = main_table.factory_id',
                'default_store_data.store_id = 0'
            ]),
            []);

        // Join current store data
        if ($storeId != 0) {
            $connection = $collection->getConnection();
            $select->joinLeft(
                ['current_store_data' => $this->getTable()],
                implode(' AND ', [
                    'current_store_data.factory_id = main_table.factory_id',
                    $connection->quoteInto('current_store_data.store_id = ?', $storeId)
                ]),
                []);
        }

        // Add select columns
        $columns = array_combine(
            $this->fields,
            array_map(function($field) use ($storeId) {
                return $this->getFieldExpr($field, $storeId);
            }, $this->fields));
        $select->columns($columns);
    }

    public function getFieldExpr($field, $storeId)
    {
        return ($storeId == 0)
            ? sprintf('default_store_data.%s', $field)
            : sprintf('IFNULL(current_store_data.%1$s, default_store_data.%1$s)', $field);
    }

    protected function getDefaultData()
    {
        return array_fill_keys($this->fields, null);
    }
}
