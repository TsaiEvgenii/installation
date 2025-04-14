<?php


namespace BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \BelVG\LayoutCustomizer\Model\LayoutStore::class,
            \BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore::class
        );
    }

    public function addLayoutFilter($layoutId)
    {
        return $this->addFieldToFilter('layout_id', $layoutId);
    }
}
