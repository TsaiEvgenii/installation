<?php


namespace BelVG\LayoutCustomizer\Api\Service;


interface StoreFieldsInterface
{
    /**
     * @return array
     */
    public function getStoreSeparatedFields();

    /**
     * @param $field
     * @param int $storeId
     * @param string $storeDbTableAlias
     * @param string $defaultDbTableAlias
     * @return \Zend_Db_Expr
     */
    public function getZendDbExprForField($field, $storeId = 0, $storeDbTableAlias = 'layoutstore', $defaultDbTableAlias = 'defaultstore');

    /**
     * @param int $storeId
     * @param string $storeDbTableAlias
     * @param string $defaultDbTableAlias
     * @return array
     */
    public function getZendDbExprForAll($storeId = 0, $storeDbTableAlias = 'layoutstore', $defaultDbTableAlias = 'defaultstore');
}
