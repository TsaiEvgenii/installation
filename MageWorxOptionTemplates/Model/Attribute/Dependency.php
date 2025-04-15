<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
namespace BelVG\MageWorxOptionTemplates\Model\Attribute;

use MageWorx\OptionDependency\Model\Config;
use MageWorx\OptionDependency\Model\Attribute\Dependency as MageWorxDependency;

class Dependency extends MageWorxDependency
{


    /**
     * {@inheritdoc}
     */
    protected function addData(&$data, $object)
    {
        $childOptionId = isset($object['mageworx_option_id']) ? $object['mageworx_option_id'] : null;
        $childOptionTypeId = isset($object['mageworx_option_type_id']) ? $object['mageworx_option_type_id'] : '';
        $dataObjectId = $this->entity->getDataObjectId();
        $fieldHiddenDependency = isset($object['field_hidden_dependency']) ? $object['field_hidden_dependency'] : null;

        // why shouldn't we delete dependency if it does not exist
        //there is an extra if line 60
//        if (is_null($fieldHiddenDependency)) {
//            return;
//        }


        $groupId = null;
        if ($this->entity->getType() == 'product') {
            $groupId           = $this->registry->registry('mageworx_optiontemplates_group_id');
            $data['delete'][]  = [
                Config::COLUMN_NAME_PRODUCT_ID => $dataObjectId,
                Config::COLUMN_NAME_GROUP_ID   => $groupId ? $groupId : 0,
            ];
        } else {
            $data['delete'][] = [
                Config::COLUMN_NAME_PRODUCT_ID => 0,
                Config::COLUMN_NAME_GROUP_ID => $dataObjectId,
            ];
        }

        if (!$fieldHiddenDependency) {
            return;
        }

        $savedDependencies = $this->jsonHelper->jsonDecode($fieldHiddenDependency);
        if ($this->entity->getType() == 'product') {
            $savedDependencies = $this->convertDependencies($savedDependencies, $dataObjectId);
        }

        // delete non-existent options from dependencies
        $savedDependencies = $this->processDependencies($savedDependencies);
        $savedDependencies = $this->convertRecordIdToMageworxId($savedDependencies);

        foreach ($savedDependencies as $dependency) {
            $parentOptionId = $dependency[0];
            $parentOptionTypeId = $dependency[1];
            if ($this->entity->getType() == 'product') {

                $groupOptionIds = $this->registry->registry('mageworx_optiontemplates_group_option_ids');
                if ($groupOptionIds) {
                    if (!$object['group_option_id']
                        || !in_array($object['group_option_id'], $groupOptionIds)
                        || (!$groupId && !empty($object['group_id']))
                    ) {
                        continue;
                    }
                }

                if (!empty($object['group_id'])) {
                    $groupId = $object['group_id'];
                }
                $currentKey = implode('-',
                    [
                        $childOptionId,
                        $childOptionTypeId,
                        $parentOptionId,
                        $parentOptionTypeId,
                        $dataObjectId,
                        $groupId
                    ]
                );
                    $data['save'][$currentKey] = [
                        'child_option_id' => $childOptionId,
                        'child_option_type_id' => $childOptionTypeId,
                        'parent_option_id' => $parentOptionId,
                        'parent_option_type_id' => $parentOptionTypeId,
                        $this->entity->getDataObjectIdName() => $dataObjectId,
                        'group_id' => $groupId
                    ];
            } else {
                $data['save'][] = [
                    'child_option_id' => $childOptionId,
                    'child_option_type_id' => $childOptionTypeId,
                    'parent_option_id' => $parentOptionId,
                    'parent_option_type_id' => $parentOptionTypeId,
                    $this->entity->getDataObjectIdName() => $dataObjectId
                ];
            }
        }
        return;
    }

}
