<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Model\Service\MWOT;

use Magento\Framework\App\ResourceConnection;

class MWOTOptionResourceService
{
    public function __construct(
        private ResourceConnection $resource
    ) {
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getMwotDataForProductId(
        int $productId,
        int $storeId
    ) {
        $sql = '
            SELECT prod_option.option_id, prod_option.mageworx_group_option_id,
                prod_option_value.mageworx_optiontemplates_group_option_type_id,
                prod_option_value_is_default.is_default,
                prod_option_title.title
            FROM `' . $this->resource->getTableName('catalog_product_option') . '` prod_option
            JOIN `' . $this->resource->getTableName('catalog_product_option_type_value') . '` prod_option_value ON (prod_option.option_id = prod_option_value.option_id)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_is_default') . '` prod_option_value_is_default ON (prod_option_value.mageworx_optiontemplates_group_option_type_id = prod_option_value_is_default.mageworx_option_type_id)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_value') . '` prod_option_type_value ON (prod_option_type_value.mageworx_option_type_id = prod_option_value.mageworx_optiontemplates_group_option_type_id)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_title') . '` prod_option_title ON (prod_option_title.option_type_id = prod_option_type_value.option_type_id)
            WHERE prod_option.product_id = '.(int)$productId.'
                AND prod_option_value_is_default.store_id = '.(int)$storeId.'
                AND prod_option_title.store_id = '.(int)$storeId.'
        ';

        return $this->resource->getConnection()->fetchAll($sql);
    }

    /**
     * @param int $optionId
     * @param int $storeId
     * @return array
     */
    public function getMwotDataForProductOptionId(
        int $optionId,
        int $storeId
    ) {
        $sql = '
            SELECT prod_option.option_id, prod_option.mageworx_group_option_id,
                prod_option_value.mageworx_optiontemplates_group_option_type_id,
                prod_option_value_is_default.is_default,
                prod_option_title.title
            FROM `' . $this->resource->getTableName('catalog_product_option') . '` prod_option
            JOIN `' . $this->resource->getTableName('catalog_product_option_type_value') . '` prod_option_value ON (prod_option.option_id = prod_option_value.option_id)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_is_default') . '` prod_option_value_is_default ON (prod_option_value.mageworx_optiontemplates_group_option_type_id = prod_option_value_is_default.mageworx_option_type_id)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_value') . '` prod_option_type_value ON (prod_option_type_value.mageworx_option_type_id = prod_option_value.mageworx_optiontemplates_group_option_type_id)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_title') . '` prod_option_title ON (prod_option_title.option_type_id = prod_option_type_value.option_type_id)
            WHERE prod_option.option_id = '.(int)$optionId.'
                AND prod_option_value_is_default.store_id = '.(int)$storeId.'
                AND prod_option_title.store_id = '.(int)$storeId.'
        ';

        return $this->resource->getConnection()->fetchAll($sql);
    }
}
