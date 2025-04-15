<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Service\ProductionCapability;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\App\ResourceConnection;

class ProductionCapabilityService
{
    public function __construct(
        private ResourceConnection $resource
    ) {
    }

    /**
     * @param CartInterface $quote
     * @param $storeId
     * @return array
     */
    public function getFactoriesByQuote(CartInterface $quote, $storeId) {
        $sql = 'SELECT layout.layout_id, layout.identifier as layout_identifier, layout.family_id,
                    material.identifier as material_identifier,
                    factory_material.factory_id, factory_material.priority,
                    product.entity_id as product_id,
                    quote_item.item_id as quote_item_id, quote_item.qty
                FROM ' . $this->resource->getTableName('belvg_layoutcustomizer_layout') . ' layout
                JOIN ' . $this->resource->getTableName('belvg_layoutmaterial_layoutmaterial') . ' material ON (layout.layoutmaterial_id = material.layoutmaterial_id)
                JOIN ' . $this->resource->getTableName('belvg_factory_material') . ' factory_material ON (factory_material.material_id = layout.layoutmaterial_id)
                JOIN ' . $this->resource->getTableName('catalog_product_entity') . ' product ON (product.sku = layout.identifier)
                JOIN ' . $this->resource->getTableName('belvg_factory_store') . ' factory_store ON (factory_store.factory_id = factory_material.factory_id)
                JOIN ' . $this->resource->getTableName('quote_item') . ' ON (quote_item.product_id = product.entity_id)
                WHERE quote_item.quote_id = :quoteId
                        AND factory_store.store_id = :storeId
                ORDER BY factory_material.priority DESC';
        $bind[':quoteId'] = (int)$quote->getId();
        $bind[':storeId'] = (int)$storeId;

        return $this->resource->getConnection()->fetchAll($sql, $bind);
    }

    /**
     * @param $skus
     * @param $storeId
     * @return array
     */
    public function getFactoriesBySku($skus, $storeId) {
        $connection = $this->resource->getConnection();
        $select = $connection->select()->from(
            ['layout' => $connection->getTableName('belvg_layoutcustomizer_layout')],
            [
                'layout.layout_id',
                'layout_identifier' => 'layout.identifier',
                'layout.family_id'
            ]
        )->join(
            ['material' => $this->resource->getTableName('belvg_layoutmaterial_layoutmaterial')],
            'layout.layoutmaterial_id = material.layoutmaterial_id',
            [
                'material_identifier' => 'material.identifier'
            ]
        )->join(
            ['factory_material' => $this->resource->getTableName('belvg_factory_material')],
//Todo: check if adding store_id condition works ok for all cases
//            'factory_material.material_id = layout.layoutmaterial_id AND factory_material.store_id = ' . $storeId,
            'factory_material.material_id = layout.layoutmaterial_id',
            [
                'factory_material.factory_id',
                'factory_material.priority'
            ]
        )->join(
            ['product' => $this->resource->getTableName('catalog_product_entity')],
            'product.sku = layout.identifier',
            [
                'product_id' => 'product.entity_id'
            ]
        )->join(
            ['factory_store' => $this->resource->getTableName('belvg_factory_store')],
            'factory_store.factory_id = factory_material.factory_id AND factory_store.is_active = 1',
            [
                'material_identifier' => 'material.identifier'
            ]
        )->where(
            'layout.identifier IN (?)',
            $skus
        )->where(
            'factory_store.store_id = ?',
            $storeId
        );

        return $this->resource->getConnection()->fetchAll($select);
    }
}
