<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Option extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'belvg_layoutcustomizer_layout_block_parameter_option',
            'option_id');
    }

    public function updateOptions($parameterId, array $options)
    {
        $valuesByOptionTypeId = [];
        foreach ($options as $option) {
            if (!empty($option['id'])) {
                $optionTypeId = $option['id'];
                $value = isset($option['value']) ? $option['value'] : '';
                $valuesByOptionTypeId[$optionTypeId] = $value;
            }
            // ignore options with empty IDs
        }
        $this->saveMultiple($parameterId, $valuesByOptionTypeId);
        $this->deleteOtherOptionTypeIds($parameterId, array_keys($valuesByOptionTypeId));
    }

    protected function saveMultiple($parameterId, array $valuesByOptionTypeId)
    {
        $data = [];
        $sortOrder = 0;
        foreach ($valuesByOptionTypeId as $optionTypeId => $value) {
            $data[] = [
                'parameter_id'   => $parameterId,
                'option_type_id' => $optionTypeId,
                'value'          => $value,
                'sort_order'     => $sortOrder++
            ];
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
        }
    }

    protected function deleteOtherOptionTypeIds($parameterId, array $optionTypeIds)
    {
        $where = ['parameter_id = ?' => $parameterId];
        if (!empty($optionTypeIds)) {
            $where['option_type_id NOT IN (?)'] = $optionTypeIds;
        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
