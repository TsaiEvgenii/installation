<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use BelVG\LayoutCustomizer\Model\Layout\Measurement as Model;

class Measurement extends AbstractDb
{
    protected $paramResource;

    public function __construct(
        Measurement\Param $paramResource,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null)
    {
        $this->paramResource = $paramResource;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init(
            'belvg_layoutcustomizer_layout_measurement',
            'measurement_id');
    }

    public function deleteOther($blockId, array $ids)
    {
        $where = ['block_id = ?' => $blockId];
        if (!empty($ids)) {
            $where['measurement_id NOT IN (?)'] = $ids;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->saveParams($object);
        return parent::_afterSave($object);
    }

    protected function saveParams(Model $measurement)
    {
        $this->paramResource->updateParams(
            $measurement->getId(),
            $measurement->getParams());
    }
}
