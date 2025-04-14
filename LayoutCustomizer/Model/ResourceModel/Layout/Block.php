<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use BelVG\LayoutCustomizer\Model\Layout\Block as Model;

class Block extends AbstractDb
{
    protected $shapeParamResource;

    public function __construct(
        Block\ShapeParam $shapeParamResource,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null)
    {
        $this->shapeParamResource = $shapeParamResource;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('belvg_layoutcustomizer_layout_block', 'block_id');
    }

    public function deleteOther($layoutId, array $ids)
    {
        $where = ['layout_id = ?' => $layoutId];
        if (!empty($ids)) {
            $where['block_id NOT IN (?)'] = $ids;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->saveShapeParams($object);
        return parent::_afterSave($object);
    }

    protected function saveShapeParams(Model $block)
    {
        $this->shapeParamResource->updateParams(
            $block->getId(),
            $block->getShapeParams());
    }
}
