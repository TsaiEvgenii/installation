<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023-2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Model\CollectionUpdater\Value;


use MageWorx\OptionBase\Model\Product\Option\AbstractUpdater;

class MadeInDenmarkPrice extends AbstractUpdater
{
    const FIELD       = 'mageworx_option_type_made_in_denmark_price';
    const TABLE_ALIAS = 'mageworx_option_type_made_in_denmark_price';

    const OPTIONTEMPLATES_TABLE_NAME    = 'mageworx_optiontemplates_group_option_type_made_in_denmark_price';

    const FIELD_OPTION_TYPE_ID          = 'option_type_id';


    public function getFromConditions(array $conditions)
    {
        $alias = $this->getTableAlias();
        $table = $this->getTable($conditions);
        return [$alias => $table];
    }

    public function getTableName($entityType): string
    {
        return $this->resource->getTableName(self::OPTIONTEMPLATES_TABLE_NAME);
    }

    public function getOnConditionsAsString(): string
    {
        return sprintf(
            'main_table.%1$s = %2$s.%1$s',
            self::FIELD_OPTION_TYPE_ID,
            self::TABLE_ALIAS);
    }

    public function getColumns(): array
    {
        return [self::FIELD => self::TABLE_ALIAS . '.' . self::FIELD];
    }

    public function getTableAlias()
    {
        return self::TABLE_ALIAS;
    }

    private function getTable($conditions)
    {
        $entityType = $conditions['entity_type'];
        $tableName = $this->getTableName($entityType);

        $this->resource->getConnection()->query('SET SESSION group_concat_max_len = 10000000;');

        $selectExpr = 'SELECT ' . self::FIELD_OPTION_TYPE_ID . ' AS '
            . self::FIELD_OPTION_TYPE_ID . ', '
            . 'CONCAT("[",'
            . 'GROUP_CONCAT(CONCAT("{",'
            . '"\"store_id\":", store_id, ",\"price\":", price, ",\"price_type\":\"", price_type,'
            . ' "\"}")),'
            . '"]") AS ' . self::TABLE_ALIAS . ' FROM ' . $tableName;
        if (!empty($conditions['option_id']) || !empty($conditions['value_id'])) {
            $ids = $this->helper->findOptionTypeIdByConditions($conditions, false);

            if ($ids) {
                $selectExpr .= " WHERE option_type_id IN(" . implode(',', $ids) . ")";
            }
        }
        $selectExpr .= ' GROUP BY option_type_id';

        return new \Zend_Db_Expr('(' . $selectExpr . ')');
    }

}