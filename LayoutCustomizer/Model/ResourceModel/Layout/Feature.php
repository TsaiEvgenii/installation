<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use BelVG\LayoutCustomizer\Model\Layout\Feature as Model;

class Feature extends AbstractDb
{
    protected $paramResource;

    public function __construct(
        Feature\Param $paramResource,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null)
    {
        $this->paramResource = $paramResource;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('belvg_layoutcustomizer_layout_feature', 'feature_id');
    }

    public function deleteOther($blockId, array $ids)
    {
        $where = ['block_id = ?' => $blockId];
        if (!empty($ids)) {
            $where['feature_id NOT IN (?)'] = $ids;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->saveParams($object);
        return parent::_afterSave($object);
    }

    protected function saveParams(Model $feature)
    {
        $this->paramResource->updateParams(
            $feature->getId(),
            $feature->getParams());
    }
}
