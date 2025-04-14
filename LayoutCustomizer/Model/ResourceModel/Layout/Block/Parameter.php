<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Belvg\LayoutCustomizer\Model\Layout\Block\Parameter as Model;

class Parameter extends AbstractDb
{
    protected $optionResource;

    public function __construct(
        Parameter\Option $optionResource,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null)
    {
        $this->optionResource = $optionResource;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('belvg_layoutcustomizer_layout_block_parameter', 'parameter_id');
    }

    public function deleteOther($blockId, array $ids)
    {
        $where = ['block_id = ?' => $blockId];
        if (!empty($ids)) {
            $where['parameter_id NOT IN (?)'] = $ids;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->saveOptions($object);
        return parent::_afterSave($object);
    }

    protected function saveOptions(Model $parameter)
    {
        $this->optionResource->updateOptions(
            $parameter->getId(),
            $parameter->getData('options'));
    }
}
