<?php
namespace BelVG\LayoutCustomizer\Helper\Layout;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\Option\CollectionFactory
    as MwOptionCollectionFactory;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\CollectionFactory
    as MwGroupCollectionFactory;
use BelVG\LayoutCustomizer\Helper\Data as DataHelper;

class ExtParams
{
    protected $optionCollectionFactory;
    protected $groupCollectionFactory;
    protected $dataHelper;

    public function __construct(
        MwOptionCollectionFactory $optionCollectionFactory,
        MwGroupCollectionFactory $groupCollectionFactory,
        DataHelper $dataHelper)
    {
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->dataHelper = $dataHelper;
    }

    public function getOptionTree($storeId)
    {
        return $this->getMageworxOptionTree($storeId);
    }

    protected function getMageworxOptionTree($storeId)
    {
        $optionCollection = $this->optionCollectionFactory
            ->create()
            ->addFieldToFilter('type', ['in' => ['field']])
            ->addTitleToResult($storeId)
            ->addGroupValuesToResult($storeId);
        $groupIds = array_unique($optionCollection->getColumnValues('group_id'));

        $groupCollection = $this->groupCollectionFactory
            ->create()
            ->addFieldToFilter('group_id', ['in' => $groupIds]);

        return array_map(function($group) use ($optionCollection) {
            return [
                'label'    => $group->getTitle(),
                'children' => array_map(function($option) {
                    return [
                        'id' => $option->getId(),
                        'label'    => $option->getTitle()
                    ];
                }, $optionCollection->getItemsByColumnValue('group_id', $group->getId()))
            ];
        }, array_values($groupCollection->getItems()));
    }

    public function getOverallWidthParamId()
    {
        return $this->dataHelper->getOverallWidthParamId();
    }

    public function getOverallHeightParamId()
    {
        return $this->dataHelper->getOverallHeightParamId();
    }
}
