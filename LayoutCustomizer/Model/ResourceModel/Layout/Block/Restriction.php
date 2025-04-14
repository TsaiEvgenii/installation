<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block;

use BelVG\LayoutCustomizer\Model\Layout\Block\Restriction as Model;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Restriction extends AbstractDb
{
    protected $paramResource;

    public function __construct(
        Restriction\Param $paramResource,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null)
    {
        $this->paramResource = $paramResource;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init(
            'belvg_layoutcustomizer_layout_block_restriction',
            'restriction_id');
    }

    public function deleteOther($blockId, array $ids)
    {
        $where = ['block_id = ?' => $blockId];
        if (!empty($ids)) {
            $where['restriction_id NOT IN (?)'] = $ids;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->saveParams($object);
        return parent::_afterSave($object);
    }

    protected function saveParams(Model $restriction)
    {
        $this->paramResource->updateParams(
            $restriction->getId(),
            $restriction->getParams());
    }
}
