<?php


namespace BelVG\LayoutCustomizer\Ui\DataProvider\Product\Grid;


class AddBelVGLayoutFieldToCollection implements \Magento\Ui\DataProvider\AddFieldToCollectionInterface
{
    protected $eavAttribute;
    protected $request;
    protected $resources;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $dbContext,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->eavAttribute = $eavAttribute;
        $this->request = $request;

        $this->resources = $dbContext->getResources();
    }


    public function addField(\Magento\Framework\Data\Collection $collection, $field, $alias = null) {
//        $collection->addAttributeToSelect($field);
//        $collection->joinField( $field_name, 'belvg_layoutcustomizer_layout', $field_name, 'layout_id=at_belvg_layout.value', null, 'left' );

        $attributeId = $this->eavAttribute->getIdByCode('catalog_product', \BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR);
        $storeId = $this->request->getParam('store');

        $collection->getSelect()
            ->joinLeft(
                ['at_belvg_layout' => $this->resources->getTableName('catalog_product_entity_int')],
                'at_belvg_layout.entity_id = e.entity_id 
                    AND (`at_belvg_layout`.`attribute_id` = \''.(int)$attributeId.'\')
                    AND (`at_belvg_layout`.`store_id` = '.(int)$storeId.')',
                ['belvg_layout' => 'at_belvg_layout.value']
            )->joinLeft(
                ['layout' => $this->resources->getTableName('belvg_layoutcustomizer_layout')],
                'layout.layout_id = at_belvg_layout.value',
                ['layout.identifier']
            );

        //sort order
        $sorting = $this->request->getParam('sorting');
        if (isset($sorting['field']) && $sorting['field'] == \BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR) {
            $collection->getSelect()->order('layout.identifier ' . $sorting['direction']);
        }
    }
}
