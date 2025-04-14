<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Belvg\LayoutCustomizer\Model\Layout\Block\Link as Model;

class Link extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('belvg_layoutcustomizer_layout_block_link', 'link_id');
    }

    public function deleteOther($blockId, array $ids)
    {
        $where = ['block_id = ?' => $blockId];
        if (!empty($ids)) {
            $where['link_id NOT IN (?)'] = $ids;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
