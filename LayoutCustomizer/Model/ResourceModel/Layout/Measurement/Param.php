<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Param extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'belvg_layoutcustomizer_layout_measurement_param',
            'param_id');
    }

    public function updateParams($measurementId, array $params)
    {
        $this->saveMultiple($measurementId, $params);
        $this->deleteOtherNames($measurementId, array_keys($params));
    }

    protected function saveMultiple($measurementId, array $params)
    {
        $data = [];
        foreach ($params as $name => $value) {
            $data[]              = [
                'measurement_id' => $measurementId,
                'name'           => $name,
                'value'          => $value,
            ];
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
        }
    }

    protected function deleteOtherNames($measurementId, array $names)
    {
        $where = ['measurement_id = ?' => $measurementId];
        if (!empty($names)) {
            $where['name NOT IN (?)'] = $names;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
