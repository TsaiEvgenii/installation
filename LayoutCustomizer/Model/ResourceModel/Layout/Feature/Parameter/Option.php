<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Option
 * @package BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter
 */
class Option extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'belvg_layoutcustomizer_layout_feature_parameter_option',
            'option_id');
    }

    public function updateOptions($parameterId, array $options)
    {
        $valuesByOptionTypeId = [];
        foreach ($options as $key => $option) {
            if (!empty($option['id'])) {
                $valuesByOptionTypeId[$key]['option_type_id'] = $option['id'];
                $valuesByOptionTypeId[$key]['value'] = $option['value'] ?? '';
                $valuesByOptionTypeId[$key]['key_family'] = $option['key_family'] ?? null;
                $valuesByOptionTypeId[$key]['parent_key_family'] = $option['parent_key_family'] ?? null;
            }
            // ignore options with empty IDs
        }
        $this->saveMultiple($parameterId, $this->unsetUnnessecaryOptions($valuesByOptionTypeId));
    }

    /**
     * @param array $valuesByOptionTypeId
     * @return array
     */
    protected function unsetUnnessecaryOptions(array &$valuesByOptionTypeId): array
    {
        $singleOptions = [];
        $complecatedOptions = [];
        foreach ($valuesByOptionTypeId as $key => $value) {
            if ($value['key_family']) {
                $complecatedOptions[$value['key_family']][$key] = $value['option_type_id'];
            } else {
                $singleOptions[$key] = $value;
            }
        }
        $this->unsetUnnessecarySingleOptions($singleOptions, $valuesByOptionTypeId);
        $this->unsetUnnessecaryComplecatedOptions($complecatedOptions, $valuesByOptionTypeId);
        return $valuesByOptionTypeId;
    }

    /**
     * @param array $singleOptions
     * @param array $valuesByOptionTypeId
     * @return array
     */
    protected function unsetUnnessecarySingleOptions(array $singleOptions, array &$valuesByOptionTypeId): array
    {
        $optionsTypeId = [];
        foreach ($singleOptions as $key => $value) {
            if (in_array($value['option_type_id'], $optionsTypeId)) {
                unset($valuesByOptionTypeId[$key]);
                continue;
            }
            $optionsTypeId[] = $value['option_type_id'];
        }
        return $valuesByOptionTypeId;
    }

    /**
     * @param array $complecatedOptions
     * @param array $valuesByOptionTypeId
     * @return array
     */
    protected function unsetUnnessecaryComplecatedOptions(array $complecatedOptions, array &$valuesByOptionTypeId): array
    {
        $optionsTypeId = [];
        foreach ($complecatedOptions as $key_family => $value) {
            if (count($value) > 2) {
                $counter = 1;
                foreach ($value as $key => $v) {
                    if ($counter > 2) {
                        unset($valuesByOptionTypeId[$key]);
                    }
                    $counter++;
                }
            }
            foreach ($optionsTypeId as $optionTypeId) {
                if (!count(array_diff($value, $optionTypeId))) {
                    foreach ($value as $key => $v) {
                        unset($valuesByOptionTypeId[$key]);
                    }
                    continue 2;
                }
            }
            $optionsTypeId[] = $value;
        }
        return $valuesByOptionTypeId;
    }

    protected function saveMultiple($parameterId, array $valuesByOptionTypeId)
    {
        $data = [];
        $sortOrder = 0;
        foreach ($valuesByOptionTypeId as $value) {
            $data[] = [
                'parameter_id'   => (int) $parameterId,
                'option_type_id' => (int) $value['option_type_id'],
                'value'          => $value['value'],
                'sort_order'     => $sortOrder++,
                'key_family'     => $value['key_family'],
                'parent_key_family' => $value['parent_key_family']
            ];
        }
        $this->deleteOtherOptionTypeIds($parameterId);
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
        }
    }

    protected function deleteOtherOptionTypeIds($parameterId)
    {
//        $optionTypeIds = [];
//        foreach ($valuesByOptionTypeId as $value) {
//            $optionTypeIds[] = $value['option_type_id'];
//        }
        $where = ['parameter_id = ?' => $parameterId];
//        if (!empty($optionTypeIds)) {
//            $where['option_type_id NOT IN (?)'] = $optionTypeIds;
//        }
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
