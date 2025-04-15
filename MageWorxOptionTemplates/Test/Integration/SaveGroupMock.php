<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionTemplates\Test\Integration;


use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use MageWorx\OptionBase\Model\Product\Attributes;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use MageWorx\OptionBase\Observer\ApplyAttributesOnGroup;
use MageWorx\OptionTemplates\Controller\Adminhtml\Group\Builder;
use MageWorx\OptionTemplates\Model\Group;
use MageWorx\OptionTemplates\Model\Group\Option;
use MageWorx\OptionTemplates\Model\Group\Source\AssignType;
use MageWorx\OptionTemplates\Model\GroupFactory;
use MageWorx\OptionTemplates\Model\OptionSaver;
use Magento\Framework\App\State as AppState;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\Option\Collection;

class SaveGroupMock
{
    private \Magento\Framework\Registry $registry;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $productCollectionFactory;
    /**
     * @var OptionSaver
     */
    private OptionSaver $optionSaver;
    /**
     * @var Builder
     */
    private Builder $groupBuilder;
    /**
     * @var GroupFactory
     */
    private GroupFactory $groupFactory;
    /**
     * @var Option
     */
    private Option $groupOptionModel;
    /**
     * @var Attributes
     */
    private Attributes $productAttributes;
    private $formData;
    private ManagerInterface $dispatchManager;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var AppState
     */
    private AppState $appState;
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;
    /**
     * @var ApplyAttributesOnGroup
     */
    private ApplyAttributesOnGroup $applyAttributesOnGroup;
    /**
     * @var Group
     */
    private Group $mageWorxGroup;
    /**
     * @var CollectionUpdaterRegistry
     */
    private CollectionUpdaterRegistry $collectionUpdaterRegistry;
    /**
     * @var Collection
     */
    private Collection $mageWorxTemplateCollection;

    /**
     * SaveGroup constructor.
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $productCollectionFactory
     * @param OptionSaver $optionSaver
     * @param Builder $groupBuilder
     * @param GroupFactory $groupFactory
     * @param Option $groupOptionModel
     * @param Attributes $productAttributes
     * @param ObjectManagerInterface $objectManager
     * @param RequestInterface $request
     * @param ApplyAttributesOnGroup $applyAttributesOnGroup
     * @param Group $mageWorxGroup
     * @param CollectionUpdaterRegistry $collectionUpdaterRegistry
     * @param AppState $appState
     */
    public function __construct(\Magento\Framework\Registry $registry,
                                CollectionFactory $productCollectionFactory,
                                OptionSaver $optionSaver,
                                Builder $groupBuilder,
                                GroupFactory $groupFactory,
                                Option $groupOptionModel,
                                Attributes $productAttributes,
                                ObjectManagerInterface $objectManager,
                                RequestInterface $request,
                                ApplyAttributesOnGroup $applyAttributesOnGroup,
                                Group $mageWorxGroup,
                                CollectionUpdaterRegistry $collectionUpdaterRegistry,
                                Collection $mageWorxTemplateCollection,
                                AppState $appState)
    {

        $this->registry = $registry;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->optionSaver = $optionSaver;
        $this->groupBuilder = $groupBuilder;
        $this->groupFactory = $groupFactory;
        $this->groupOptionModel = $groupOptionModel;
        $this->productAttributes = $productAttributes;
        $this->request = $request;
        $this->appState = $appState;
        $this->objectManager = $objectManager;
        $this->applyAttributesOnGroup = $applyAttributesOnGroup;
        $this->mageWorxGroup = $mageWorxGroup;
        $this->collectionUpdaterRegistry = $collectionUpdaterRegistry;
        $this->mageWorxTemplateCollection = $mageWorxTemplateCollection;
    }

    public function save($request)
    {
        $this->resetState();
        $this->appState->emulateAreaCode('adminhtml', [$this, '_save'], [$request]);
    }
    public function _save($request)
    {
        $this->registry->unregister('mageworx_optiontemplates_group_save');
        $this->registry->register('mageworx_optiontemplates_group_save', true);
        $this->registry->unregister('mageworx_optiontemplates_group');
        $this->registry->unregister('current_store');
        $data = $request['mageworx_optiontemplates_group'];
        $this->formData = $data;
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\Config\ScopeInterface::class
        )->setCurrentScope(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        );
        if (empty($this->formData['options'])) {
            $this->formData['options'] = [];
        }

        $isTemplateChanged = true;
        if ($isTemplateChanged) {
            $data = $this->filterData($data);
            $this->request->setParams($request);
            /** @var Group $group */
            $group = $this->groupBuilder->build($this->request);

            /**
             * Initialize product options
             */
            if (isset($data['options']) && !$group->getOptionsReadonly()) {
                $options = $this->mergeProductOptions(
                    $data['options'],
                    $request['options_use_default'] ?? []
                );
                $group->setProductOptions($options);
            }

            $group->addData($data);
            $group->setCanSaveCustomOptions(
                (bool)$group->getData('affect_product_custom_options') && !$group->getOptionsReadonly()
            );

            $currentGroup = $group;
        } else {
            $currentGroup = $originalGroup;
        }

        /**
         * Initialize product relation
         */
        $productIdsData = $request['group_products'];
        if (is_null($productIdsData)) {
            $productIds = $currentGroup->getProducts();
        } else {
            $productIds = $this->getProductIds($productIdsData);
        }
        $productIds = $this->addProductsByIdSku($data, $productIds);
        $currentGroup->setProductsIds($productIds);

        $oldGroupCustomOptions = $currentGroup->getOptionArray();
        $this->registry->unregister('mageworx_optiontemplates_group_id');
        $this->registry->unregister('mageworx_optiontemplates_group_option_ids');
        $dispatchManager = $this->objectManager->create(\Magento\Framework\Event\ManagerInterface::class);
        if ($isTemplateChanged) {
            $dispatchManager->dispatch(
                'mageworx_optiontemplates_group_save_before',
                [
                    'group' => $currentGroup,
                ]
            );
            $currentGroup->save();
            $this->registry->register('mageworx_optiontemplates_group_id', $currentGroup->getId());
            $dispatchManager->dispatch(
                'mageworx_optiontemplates_group_save_after',
                [
                    'group' => $currentGroup,
                ]
            );
            $this->optionSaver->saveProductOptions(
                $currentGroup,
                $oldGroupCustomOptions,
                OptionSaver::SAVE_MODE_UPDATE
            );

        } else {
            if (!empty($data['title'])) {
                $currentGroup->saveTitle($data['title']);
            }
            $this->registry->register('mageworx_optiontemplates_group_id', $currentGroup->getId());
            $currentGroup->setProductRelation(false);
            $this->optionSaver->saveProductOptions(
                $currentGroup,
                $oldGroupCustomOptions,
                OptionSaver::SAVE_MODE_ADD_DELETE
            );
        }

        $this->registry->unregister('mageworx_optiontemplates_group_save');
        $this->registry->unregister('mageworx_optiontemplates_group_id');

        return true;
    }

    protected function addProductsByIdSku($data, $assignedProductIds)
    {
        $productIds = [];

        if ($data['assign_type'] == AssignType::ASSIGN_BY_GRID) {
            return $assignedProductIds;
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_IDS) {
            $productIds = $this->convertMultiStringToArray($data['productids'], 'intval');
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_SKUS) {
            $productSkus = $this->convertMultiStringToArray($data['productskus']);

            if ($productSkus) {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this->productCollectionFactory->create();
                $collection->addFieldToFilter('sku', ['in' => $productSkus]);
                $productIds = array_map('intval', $collection->getAllIds());
            }
        }

        return array_merge($assignedProductIds, $productIds);
    }

    /**
     * Merge product and default options for product
     *
     * @param array $productOptions product options
     * @param array $overwriteOptions default value options
     * @return array
     */
    protected function mergeProductOptions($productOptions, $overwriteOptions)
    {
        if (!is_array($productOptions)) {
            $productOptions = [];
        }
        if (is_array($overwriteOptions)) {
            $options = array_replace_recursive($productOptions, $overwriteOptions);
            array_walk_recursive($options, function (&$item) {
                if ($item === "") {
                    $item = null;
                }
            });
        } else {
            $options = $productOptions;
        }

        return $options;
    }

    protected function getProductIds($data)
    {
        if (!empty($data)) {
            $productIds = json_decode($data, true);
        } else {
            $productIds = [];
        }

        return $productIds;
    }


    protected function filterData($data)
    {
        if (isset($data['group_id']) && !$data['group_id']) {
            unset($data['group_id']);
        }

        if (isset($data['options'])) {
            $updatedOptions = [];
            foreach ($data['options'] as $key => $option) {
                if (!isset($option['option_id'])) {
                    continue;
                }

                $optionId = $option['option_id'];
                if (!$optionId && !empty($option['record_id'])) {
                    $optionId = $option['record_id'] . '_';
                }
                $updatedOptions[$optionId] = $option;
                if (empty($option['values'])) {
                    continue;
                }

                $values = $option['values'];
                foreach ($option['values'] as $valueKey => $value) {
                    if (!isset($value['option_type_id'])) {
                        continue;
                    }
                    unset($updatedOptions[$optionId]['values'][$valueKey]);
                }
                foreach ($values as $valueKey => $value) {
                    if (!isset($value['option_type_id'])) {
                        continue;
                    }
                    $valueId = $value['option_type_id'];
                    $updatedOptions[$optionId]['values'][$valueId] = $value;
                }
            }

            $data['options'] = $updatedOptions;

        }
        return $data;
    }

    private function convertMultiStringToArray($string, $finalFunction = null)
    {
        if (!trim($string)) {
            return [];
        }

        $rawLines = array_filter(preg_split('/\r?\n/', $string));
        $rawLines = array_map('trim', $rawLines);
        $lines = array_filter($rawLines);

        if (!$lines) {
            return [];
        }

        $array = [];
        foreach ($lines as $line) {
            $rawIds = explode(',', $line);
            $rawIds = array_map('trim', $rawIds);
            $lineIds = array_filter($rawIds);
            if (!$finalFunction) {
                $lineIds = array_map($finalFunction, $lineIds);
            }
            $array = array_merge($array, $lineIds);
        }

        return $array;
    }

    private function resetState()
    {
        $this->resetMageWorxOptionSaver();
        $this->resetMageWorxOptionGroup();
    }

    private function resetMageWorxOptionSaver()
    {
        $property = new \ReflectionProperty($this->applyAttributesOnGroup, 'options');
        $property->setAccessible(true);
        $property->setValue($this->applyAttributesOnGroup, []);
    }

    private function resetMageWorxOptionGroup()
    {

        $property = new \ReflectionProperty($this->mageWorxGroup, 'options');
        $property->setAccessible(true);
        $property->setValue($this->mageWorxGroup, []);
        $property = new \ReflectionProperty($this->mageWorxGroup, 'optionsInitialized');
        $property->setAccessible(true);
        $property->setValue($this->mageWorxGroup, false);
        
        $property = new \ReflectionProperty($this->mageWorxGroup, 'groupOptionInstance');
        $property->setAccessible(true);
        $property->setValue($this->mageWorxGroup, null);


        $this->collectionUpdaterRegistry->setOptionIds([]);
        $this->collectionUpdaterRegistry->setOptionValueIds([]);


        $this->mageWorxTemplateCollection->clear();

    }
}