<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ShapeParam extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'belvg_layoutcustomizer_layout_block_shape_param',
            'param_id');
    }

    public function updateParams($blockId, array $params)
    {
        $this->saveMultiple($blockId, $params);
        $this->deleteOtherNames($blockId, array_keys($params));
    }

    protected function saveMultiple($blockId, array $params)
    {
        $data = [];
        foreach ($params as $name => $value) {
            $data[] = [
                'block_id' => $blockId,
                'name'     => $name,
                'value'    => $value,
            ];
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
        }
    }

    protected function deleteOtherNames($blockId, array $names)
    {
        $where = ['block_id = ?' => $blockId];
        if (!empty($names)) {
            $where['name NOT IN (?)'] = $names;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
