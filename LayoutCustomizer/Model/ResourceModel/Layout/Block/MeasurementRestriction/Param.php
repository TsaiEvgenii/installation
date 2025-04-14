<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Param extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'belvg_layoutcustomizer_layout_block_measurement_restrict_param',
            'param_id');
    }

    public function updateParams($featureId, array $params)
    {
        $this->saveMultiple($featureId, $params);
        $this->deleteOtherNames($featureId, array_keys($params));
    }

    protected function saveMultiple($measurementRestrictionId, array $params)
    {
        $data = [];
        foreach ($params as $name => $value) {
            $data[] = [
                'measurement_restriction_id' => $measurementRestrictionId,
                'name'           => $name,
                'value'          => $value
            ];
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
        }
    }

    protected function deleteOtherNames($restrictionId, array $names)
    {
        $where = ['measurement_restriction_id = ?' => $restrictionId];
        if (!empty($names)) {
            $where['name NOT IN (?)'] = $names;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
