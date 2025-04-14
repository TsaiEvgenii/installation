<?php


namespace BelVG\LayoutCustomizer\Ui\DataProvider\Product\Grid;


class AddBelVGLayoutFilterToCollection implements \Magento\Ui\DataProvider\AddFilterToCollectionInterface
{
    protected $resources;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $dbContext
    ) {
        $this->resources = $dbContext->getResources();
    }


    public function addFilter(\Magento\Framework\Data\Collection $collection, $field, $condition = null) {
        //$collection->addFieldToFilter($field, $condition);
        $collection->getSelect()
//            ->join(
//                ['at_belvg_layout' => $this->resources->getTableName('catalog_product_entity_int')],
//                'at_belvg_layout.entity_id = e.entity_id AND (`at_belvg_layout`.`attribute_id` = \'167\') AND (`at_belvg_layout`.`store_id` = 0)',
//                ['layout.identifier']
//            )
//            ->joinLeft(
//                ['layout' => $this->resources->getTableName('belvg_layoutcustomizer_layout')],
//                'layout.layout_id = at_belvg_layout.value',
//                ['layout.identifier']
//            )
            ->where('layout.`identifier` like ?', $condition['like']);
    }
}
