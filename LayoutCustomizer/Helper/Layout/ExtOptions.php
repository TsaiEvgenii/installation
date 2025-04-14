<?php
namespace BelVG\LayoutCustomizer\Helper\Layout;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;

class ExtOptions
{
    protected $objectManager;
    protected $moduleManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager)
    {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    public function getOptionTree($storeId)
    {
        return $this->moduleManager->isEnabled('MageWorx_OptionTemplates')
            ? $this->getMageworxOptionTree($storeId)
            : [];
    }

    protected function getMageworxOptionTree($storeId)
    {
        /**
         * @var CollectionUpdaterRegistry $collectionUpdateRegister
         */
        $collectionUpdateRegister = $this->objectManager->get(CollectionUpdaterRegistry::class);
        $collectionUpdateRegister->setCurrentEntityType('group');
        $optionCollection = $this->objectManager
            ->create(\MageWorx\OptionTemplates\Model\ResourceModel\Group\Option\Collection::class);
        $groupCollection = $this->objectManager
            ->create(\MageWorx\OptionTemplates\Model\ResourceModel\Group\Collection::class);

        $optionCollection
            ->addFieldToFilter('type', ['in' => ['drop_down', 'radio']])
            ->addTitleToResult($storeId)
            ->addGroupValuesToResult($storeId);
        $groupIds = array_unique($optionCollection->getColumnValues('group_id'));
        $groupCollection->addFieldToFilter('group_id', ['in' => $groupIds]);

        return array_map(function($group) use ($optionCollection) {
            return [
                'label'    => $group->getTitle(),
                'children' => array_map(function($option) {
                    $values = [];
                    if (is_array($option->getValues())) {
                        $values = array_values($option->getValues());
                    }
                    return [
                        'label'    => $option->getTitle(),
                        'children' => array_map(function($value) {
                            return [
                                'id'    => $value->getId(),
                                'label' => $value->getTitle()
                            ];
                        }, $values)
                    ];
                }, $optionCollection->getItemsByColumnValue('group_id', $group->getId()))
            ];
        }, array_values($groupCollection->getItems()));
    }
}
