<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Param extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('belvg_layoutcustomizer_layout_feature_param', 'param_id');
    }

    public function updateParams($featureId, array $params)
    {
        $this->saveMultiple($featureId, $params);
        $this->deleteOtherNames($featureId, array_keys($params));
    }

    protected function saveMultiple($featureId, array $params)
    {
        $data = [];
        foreach ($params as $name => $value) {
            $data[] = [
                'feature_id' => $featureId,
                'name'       => $name,
                'value'      => $value,
            ];
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
        }
    }

    protected function deleteOtherNames($featureId, array $names)
    {
        $where = ['feature_id = ?' => $featureId];
        if (!empty($names)) {
            $where['name NOT IN (?)'] = $names;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
