<?php


namespace BelVG\LayoutCustomizer\Model\ResourceModel;

class LayoutStore extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('belvg_layoutcustomizer_layoutstore', 'layoutstore_id');
    }
}