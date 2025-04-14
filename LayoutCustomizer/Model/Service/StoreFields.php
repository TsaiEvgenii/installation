<?php

namespace BelVG\LayoutCustomizer\Model\Service;

use BelVG\LayoutCustomizer\Model\Config\TaxableFieldsConfig;
use BelVG\LayoutCustomizer\Model\Service\TaxRateByStoreService;

class StoreFields implements \BelVG\LayoutCustomizer\Api\Service\StoreFieldsInterface
{
    protected $taxableFieldsConfig;
    protected $taxRateByStoreService;

    public function __construct(
        TaxableFieldsConfig $taxableFieldsConfig,
        TaxRateByStoreService $taxRateByStoreService
    ) {
        $this->taxableFieldsConfig = $taxableFieldsConfig;
        $this->taxRateByStoreService = $taxRateByStoreService;
    }

    /**
     * @return array
     */
    public function getStoreSeparatedFields(){
        return [
            'base_price',
            'sqm_price',
            'sqm_price_step2',
            'sqm_level_step2',
            'horizontal_frame',
            'vertical_frame',
            'inoutcolor_price_both_diff',
            'inoutcolor_price_both_same',
            'inoutcolor_price_inside_otherwhite',
            'inoutcolor_price_outside_otherwhite'
        ];
    }

    /**
     * @param $field
     * @param int $storeId
     * @param string $storeDbTableAlias
     * @param string $defaultDbTableAlias
     * @return \Zend_Db_Expr
     */
    public function getZendDbExprForField(
        $field,
        $storeId = 0,
        $storeDbTableAlias = 'layoutstore',
        $defaultDbTableAlias = 'defaultstore'
    ) {
        $taxRateMultiplier = 1;
        if (in_array($field, $this->taxableFieldsConfig->getFields())) {
            $taxRateMultiplier = $this->taxRateByStoreService->getTaxRateMultiplier($storeId);
        }

        return new \Zend_Db_Expr(sprintf('(IF('.$storeDbTableAlias.'.'.$field.' is NOT NULL, '.$storeDbTableAlias.'.'.$field.', '.$defaultDbTableAlias.'.'.$field.')) * %1$f', $taxRateMultiplier));
    }

    /**
     * @param int $storeId
     * @param string $storeDbTableAlias
     * @param string $defaultDbTableAlias
     * @return array
     */
    public function getZendDbExprForAll(
        $storeId = 0,
        $storeDbTableAlias = 'layoutstore',
        $defaultDbTableAlias = 'defaultstore'
    ) {
        $result = [];
        foreach ($this->getStoreSeparatedFields() as $field) {
            $result[$field] = $this->getZendDbExprForField($field, $storeId, $storeDbTableAlias, $defaultDbTableAlias);
        }
        unset($field);

        return $result;
    }
}
