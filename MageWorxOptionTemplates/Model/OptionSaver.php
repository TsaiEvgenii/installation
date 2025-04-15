<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\ReadHandler;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface as OptionRepository;
use Magento\Framework\Webapi\Exception;
use MageWorx\OptionBase\Helper\System as SystemHelper;
use MageWorx\OptionTemplates\Helper\Data as Helper;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use MageWorx\OptionTemplates\Model\ResourceModel\Product as ResourceModelProduct;
use MageWorx\OptionBase\Model\AttributeSaver;
use MageWorx\OptionBase\Model\ResourceModel\DataSaver;
use MageWorx\OptionTemplates\Model\OptionSaver as MageWorxOptionSaver;
use MageWorx\OptionTemplates\Model\Group;
use \Magento\Store\Model\StoreManagerInterface;
use MageWorx\OptionTemplates\Model\Group\OptionFactory;
use MageWorx\OptionTemplates\Model\Group\Option\ValueFactory as OptionValueFactory;
use BelVG\MageWorxOptionTemplates\Model\Log\Item as LogItem;

/**
 * Class OptionSaver
 * @package BelVG\MageWorxOptionTemplates\Model
 */
class OptionSaver extends MageWorxOptionSaver
{

    const SAVE_MODE_FORCE = 'force';

    /**
     * @var ScheduleBulk
     */
    protected $scheduleBulk;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Logger
     */
    protected $localLogger;

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @var OptionValueFactory
     */
    protected $optionValueFactory;

    /**
     * @var SystemHelper
     */
    protected $systemHelper;

    protected $base;

    protected $infoLogger;

    protected $currentOptionsIds;

    protected static $forceUpdateOptionsExcludedFields = [
        'id',
        'item_count',
        'option_id',
        'group_option_id',
        'mageworx_group_option_id'
    ];
    /**
     * @var array
     */
    protected static $forceUpdateOptionValuesExcludedFields = [
        'item_count',
        'option_id',
        'option_type_id',
        'group_option_value_id',
        'mageworx_option_type_id',
        'mageworx_group_option_type_id'
    ];

    /**
     *
     * @param \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig
     * @param \MageWorx\OptionTemplates\Model\GroupFactory $groupFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory $customOptionFactory
     * @param OptionRepository $optionRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param ManagerInterface $eventManager
     * @param ResourceConnection $resource
     * @param CollectionUpdaterRegistry $collectionUpdaterRegistry
     * @param ResourceModelProduct $resourceModelProduct
     * @param \MageWorx\OptionTemplates\Model\OptionSaver\Option $optionDataCollector
     * @param AttributeSaver $attributeSaver
     * @param DataSaver $dataSaver
     */
    public function __construct(
        ReadHandler $readHandler,
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
        \MageWorx\OptionTemplates\Model\GroupFactory $groupFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory $customOptionFactory,
        OptionRepository $optionRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Psr\Log\LoggerInterface $logger,
        Helper $helper,
        BaseHelper $baseHelper,
        \MageWorx\OptionTemplates\Model\Group\Source\SystemAttributes $systemAttributes,
        \MageWorx\OptionBase\Model\Entity\Group $groupEntity,
        \MageWorx\OptionBase\Model\Entity\Product $productEntity,
        ManagerInterface $eventManager,
        CollectionUpdaterRegistry $collectionUpdaterRegistry,
        ResourceConnection $resource,
        ResourceModelProduct $resourceModelProduct,
        \MageWorx\OptionTemplates\Model\OptionSaver\Option $optionDataCollector,
        AttributeSaver $attributeSaver,
        DataSaver $dataSaver,
        ScheduleBulk $scheduleBulk,
        StoreManagerInterface $storeManager,
        Logger $localLogger,
        SystemHelper $systemHelper,
        OptionFactory $optionFactory,
        OptionValueFactory $optionValueFactory,
        Log\InfoLogger $infoLogger,
        Entity\Base $base
    )
    {
        parent::__construct($readHandler,
            $productOptionConfig,
            $groupFactory,
            $productCollectionFactory,
            $customOptionFactory,
            $optionRepository,
            $productRepository,
            $logger,
            $helper,
            $baseHelper,
            $systemAttributes,
            $groupEntity,
            $productEntity,
            $eventManager,
            $collectionUpdaterRegistry,
            $resource,
            $resourceModelProduct,
            $optionDataCollector,
            $attributeSaver,
            $dataSaver
        );
        $this->base = $base;
        $this->infoLogger = $infoLogger;
        $this->scheduleBulk = $scheduleBulk;
        $this->storeManager = $storeManager;
        $this->localLogger = $localLogger;
        $this->systemHelper = $systemHelper;
        $this->optionFactory = $optionFactory;
        $this->optionValueFactory = $optionValueFactory;
        $this->setEmptyActions();
    }

    /**
     * Map of possible attributes
     *
     * @return array
     */
    public function getOptionValuesMap()
    {
        return [
            'title',
            'type',
            'is_require',
            'sort_order',
            'can_edit_price',
            'price',
            'price_type',
            'sku',
            'max_characters',
            'file_extension',
            'image_size_x',
            'image_size_y',
//'values',
            'option_title_id',
            'dependency_type',
            'one_time',
            'qty_input',
            'description',
            'mageworx_option_gallery',
            'mageworx_option_image_mode',
            'sku_policy',
            'is_swatch',
            'is_all_groups',
            'is_all_websites',
            'disabled',
            'disabled_by_values',
            'hidden',
            'inside_outside_color', //25.05.2019
            'warning_msg', //25.05.2019
            'mageworx_group_option_id',
            'item_count',
            // 'mageworx_option_id',
        ];
    }

    /**
     * Modify product options using template options
     * Save mode 'add_delete': add template options to new products, delete template options from unassigned products
     * Save mode 'update': similar to 'add_delete' + rewrite template options on existing products
     * Save to queue
     *
     * @param Group $group
     * @param array $oldGroupCustomOptions
     * @param string $saveMode
     * @param int $storeId
     * @return void
     */
    public function saveProductOptions(Group $group, $oldGroupCustomOptions, $saveMode, $storeId = null)
    {
        try {
            $data = [];
            $this->group = $group;
            $this->oldGroupCustomOptions = $oldGroupCustomOptions;
            if ($storeId == null) {
                $storeId = $this->systemHelper->resolveCurrentStoreId();
            }
            $this->setForceOptionsDiff($group->getId(), $storeId);
            $this->products[self::KEY_NEW_PRODUCT] = $group->getNewProductIds() ? array_combine($group->getNewProductIds(), $group->getNewProductIds()) : null;
            $this->products[self::KEY_UPD_PRODUCT] = $group->getUpdProductIds() ? array_combine($group->getUpdProductIds(), $group->getUpdProductIds()) : null;
            $this->products[self::KEY_DEL_PRODUCT] = $group->getDelProductIds() ? array_combine($group->getDelProductIds(), $group->getDelProductIds()) : null;
            $productIds = $group->getAffectedProductIds();

            $groupId = $group->getId();
            foreach ($productIds as $productId) {
                $data[$productId]['productId'] = $productId;
                $data[$productId]['action'] = $this->getProductAction($productId);
                $data[$productId]['groupId'] = $groupId;
                $data[$productId]['oldGroupCustomOptions'] = $oldGroupCustomOptions;
                $data[$productId]['saveMode'] = $saveMode;
                $data[$productId]['storeId'] = $storeId;
                $data[$productId]['options'] = $this->group->getOptionArray();
            }
            $this->processIncrementIds();
            $this->updateHasOptionsStatus();
            $this->scheduleBulk->execute($data);
        } catch (\Exception $exception) {
            $this->localLogger->cannotProcess($storeId, LogItem::QUEUE_PUBLISH, $group->getID());
        }
        return;
    }

    /**
     * Refreshing empty actions
     */
    public function setEmptyActions()
    {
        $this->products[self::KEY_NEW_PRODUCT] = [];
        $this->products[self::KEY_UPD_PRODUCT] = [];
        $this->products[self::KEY_DEL_PRODUCT] = [];
    }


    /**
     * @param $productId
     * @return string
     */
    public function getProductAction($productId)
    {
        if (isset($this->products[self::KEY_NEW_PRODUCT][$productId])) {
            return self::KEY_NEW_PRODUCT;
        } else if (isset($this->products[self::KEY_UPD_PRODUCT][$productId])) {
            return self::KEY_UPD_PRODUCT;
        } else {
            return self::KEY_DEL_PRODUCT;
        }
    }

    /**
     * @param $productId
     * @param $action
     */
    public function addProductToAction($productIds, $action)
    {
        if (is_array($productIds)) {
            $this->products[$action] = $productIds;
        } else {
            $this->products[$action][] = $productIds;
        }
    }

    /**
     * @param $collection
     * @param $saveMode
     * @param $groupId
     * @param $oldGroupCustomOptions
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateFromQueue($collection, $saveMode, $groupId, $oldGroupCustomOptions, $groupOptions, $storeId)
    {
        $this->group = $this->groupFactory->create()->load($groupId);
        $this->storeManager->setCurrentStore($storeId);
        $this->setGroupOptions($groupOptions);
        $this->checkPercentPriceOnConfigurable($this->group);
        $this->setForceOptionsDiff($groupId, $storeId);
        $this->currentOptionsIds = [];
        foreach ($this->group->getOptions() as $option) {
            $this->currentOptionsIds[] = $option->getMageworxGroupOptionId();
        }
        $this->processIncrementIds();
        $products = $this->processProducts($collection, $saveMode);
        $this->saveOptions($products);
        $this->updateHasOptionsStatus();
    }

    protected function processProducts($collection, $saveMode)
    {
        $this->optionData = [];
        $this->optionsToDelete = [];
        $products = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $customOptions = [];
            $this->clearProductGroupNewOptionIds();
            $product->setStoreId($this->storeManager->getStore()->getId());
            $preparedProductOptionArray = $this->getPreparedProductOptions($product, $saveMode);

            try {
                foreach ($preparedProductOptionArray as $preparedOption) {
                    /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterface $customOption */
                    if (is_object($preparedOption)) {
                        $customOption = $this->customOptionFactory->create(['data' => $preparedOption->getData()]);
                        $id = $preparedOption->getData('id');
                        $values = $preparedOption->getValues();
                    } elseif (is_array($preparedOption)) {
                        $customOption = $this->customOptionFactory->create(['data' => $preparedOption]);
                        $id = $preparedOption['id'];
                        $values = !empty($preparedOption['values']) ? $preparedOption['values'] : [];
                    } else {
                        throw new Exception(
                            __(
                                'The prepared option is not an instance of DataObject or array. %1 is received',
                                gettype($preparedOption)
                            )
                        );
                    }
                    if (!$this->isDelProduct($product->getId())) {
                        $customOption->setProductSku($product->getSku())
                            ->setOptionId($id)
                            ->setValues($values);
                        $customOptions[] = $customOption;
                    } else {
                        $this->optionsToDelete[] = $preparedOption['id'];
                    }

                }
                if (!empty($customOptions)) {
                    $product->setOptions($customOptions);
                    $product->setCanSaveCustomOptions(true);
                    $this->optionDataCollector->collectOptionsBeforeInsert(
                        $product,
                        $this->optionData,
                        $this->currentIncrementIds,
                        $this->optionsToDelete
                    );
                }

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->logger->critical($e->getLogMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
            $products[] = $product;
        }
        return $products;
    }


    /**
     * Save options by multiple insert
     *
     * @param array $products
     * @return void
     */
    protected function saveOptions($products)
    {
        $this->resource->getConnection()->beginTransaction();
        try {
            if ($this->optionsToDelete) {
                $condition = 'option_id IN (' . implode(',', $this->optionsToDelete) . ')';
                $this->dataSaver->deleteData('catalog_product_option', $condition);
                $condition = 'option_id IN (' . implode(',', $this->optionsToDelete) . ')';
                $this->dataSaver->deleteData('catalog_product_option_type_value', $condition);
            }

            //saving custom options to products
            foreach ($this->optionData as $tableName => $dataItem) {
                if ($tableName === 'additional_option_titles') {
                    $this->dataSaver->insertMultipleData('catalog_product_option_title', $dataItem);
                    continue;
                }
                $this->dataSaver->insertMultipleData($tableName, $dataItem);
            }

            $this->linkField = $this->baseHelper->getLinkField(ProductInterface::class);
            $this->productsWithOptions = [];
            foreach ($products as $productItem) {
                $this->updateProductData($productItem);
                $this->doProductRelationAction($productItem->getId());
            }

            //saving APO attributes to products
            $collectedData = $this->attributeSaver->getAttributeData();
            $this->attributeSaver->deleteOldAttributeData($collectedData, 'product');
            //remove deleted options
            foreach ($collectedData as $tableName => $dataArray) {
                if (empty($dataArray['save'])) {
                    continue;
                }
                foreach ($dataArray['save'] as $itemForSaveKey => $itemForSave) {
                    if (isset($itemForSave['option_id']) && array_search($itemForSave['option_id'],$this->optionsToDelete) !== false) {
                        unset($collectedData[$tableName]['save'][$itemForSaveKey]);
                    }
                }
            }
            foreach ($collectedData as $tableName => $dataArray) {
                if (empty($dataArray['save'])) {
                    continue;
                }
                $this->dataSaver->insertMultipleData($tableName, $dataArray['save']);
            }
            $this->resource->getConnection()->commit();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->resource->getConnection()->rollBack();
        }
        $this->attributeSaver->clearAttributeData();
    }

    /**
     * @param $groupId
     * @param $storeId
     */
    protected function setForceOptionsDiff($groupId, $storeId)
    {
        $group = $this->groupFactory->create();
        $group->load($groupId);
        $group->setStoreId($storeId);
        $oldGroupCustomOptions = $this->base->getOptionsEmptyArray($this->group);
        $this->oldGroupCustomOptions = $oldGroupCustomOptions;
        $this->groupOptions = $group->getOptionArray();
        $this->oldGroupCustomOptionValues = $this->getOptionValues($this->oldGroupCustomOptions);
        $this->deletedGroupOptions = $this->getGroupDeletedOptions();
        // TODO: rewrite function getModifyProductOptionValue and modifyOptionProcess for creation new values on force recreation
        $this->addedGroupOptions = $this->getGroupAddedOptions();
        $this->intersectedOptions = $this->getGroupIntersectedOptions();
        $groupNewModifiedOptions = $this->getGroupNewModifiedOptions();
        $groupLastModifiedOptions = $this->getGroupLastModifiedOptions();
        $this->modifiedUpGroupOptions = $this->arrayDiffRecursive(
            $groupNewModifiedOptions,
            $groupLastModifiedOptions
        );
        $this->modifiedDownGroupOptions = $this->arrayDiffRecursive(
            $groupNewModifiedOptions,
            $groupLastModifiedOptions
        );
    }

    /**
     * @param $customGroupOptions
     * @return mixed
     */
    protected function getAffectedFields($customGroupOptions)
    {
        foreach ($customGroupOptions as $optionKey => $customGroupOption) {
            foreach (self::$forceUpdateOptionsExcludedFields as $forceUpdateOptionsExcludedField) {
                unset($customGroupOptions[$optionKey][$forceUpdateOptionsExcludedField]);
            }
            if (isset($customGroupOption['values'])) {
                foreach ($customGroupOption['values'] as $valueKey => $customGroupOptionValue) {
                    foreach (self::$forceUpdateOptionValuesExcludedFields as $forceUpdateOptionValuesExcludedField) {
                        unset($customGroupOptions[$optionKey]['values'][$valueKey][$forceUpdateOptionValuesExcludedField]);
                    }
                }
            }
        }
        return $customGroupOptions;
    }

    /**
     * @param $groupOptions
     */
    protected function setGroupOptions($groupOptions)
    {
        // TODO: Refactor OptionBase\Model\Entity\Base->getOptionsAsArray and remove this function
        $_options = [];
        foreach ($groupOptions as $key => $groupOption) {
            $_option = $this->optionFactory->create()->load($key);
            foreach ($groupOption as $optionKey => $option) {
                $_option->setData($optionKey, $option);
            }
            $_values = [];
            if (isset($groupOption['values']) && $groupOption['values']) {
                foreach ($groupOption['values'] as $optionValueKey => $valueItems) {
                    $_value = $this->optionValueFactory->create()->load($optionValueKey);
                    foreach ($valueItems as $valueItemKey => $valueItem) {
                        $_value->setData($valueItemKey, $valueItem);
                    }
                    $_values[$optionValueKey] = $_value;
                }
                $_option->setValues($_values);
            }
            $_options[$key] = $_option;
        }
        $this->group->setOptions($_options);
    }


    /**
     * Fill missed attributes
     *
     * @param array $productOptions
     * @param array $ids
     * @return array
     */
    protected function changeOptionValues(array $productOptions, array $ids)
    {
        $map = $this->getOptionValuesMap();

        foreach ($ids as $_id => $_value) {
            foreach ($productOptions as $_optionIndex => &$_productOption) {
                if ($_productOption['group_option_id'] == $_id) {
                    //update values to make product option the same as template
                    foreach ($map as $m) {
                        if (isset($_productOption[$m]) && isset($_value[$m])) {
                            $_productOption[$m] = $_value[$m];
                        }
                    }
                    unset($m);
                }
            }
            unset($_optionIndex);
            unset($_productOption);
        }
        unset($_id);
        unset($_value);

        return $productOptions;
    }

    /**
     * Override reason call method `changeOptionValues`
     * Retrieve new product options as array, that were built by group modification
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $saveMode
     * @return array
     */
    public function getPreparedProductOptions($product, $saveMode)
    {
        $productOptions = $this->base->getOptionsForGroup($product, $this->currentOptionsIds);

        if ($saveMode == static::SAVE_MODE_UPDATE || $saveMode == static::SAVE_MODE_FORCE) {
            $ids = [];
            foreach ($this->groupOptions as $groupKey => $groupValue) {
                $ids[$groupKey] = $groupValue;
            }
            foreach ($productOptions as $productOption) {
                if (empty($ids[$productOption['group_option_id']]) || empty($productOption['values'])) {
                    continue;
                }
                foreach ($productOption['values'] as $valueKey => $valueData) {
                    if (empty($valueData['group_option_value_id'])) {
                        $this->addedProductValues[$productOption['group_option_id']]['values'][$valueKey] = $valueData;
                    }
                }
            }

            if ($this->isNewProduct($product->getId())) {
                $productOptions = $this->addNewOptionProcess($productOptions);
            } elseif ($this->isUpdProduct($product->getId())) {
                $productOptions = $this->deleteOptionProcess($productOptions);
                $productOptions = $this->addNewOptionProcess($productOptions);
                $productOptions = $this->modifyOptionProcess($productOptions);

                $productOptions = $this->changeOptionValues($productOptions, $ids); //override reason

            } elseif ($this->isDelProduct($product->getId())) {
                $productOptions = $this->deleteOptionProcess($productOptions);
            }
        } else {
            if ($this->isNewProduct($product->getId())) {
                $productOptions = $this->addNewOptionProcess($productOptions, $this->group);
            } elseif ($this->isDelProduct($product->getId())) {
                $productOptions = $this->deleteOptionProcess($productOptions, $this->group);
            }
        }

        return $productOptions;
    }

    /**
     * Override reason: $groupOption['id'] = null;
     * Add new options that were added in group
     *
     * @param array $productOptions
     * @param Group|null
     * @return array
     */
    public function addNewOptionProcess(array $productOptions, $group = null)
    {
        if ($group === null) {
            $groupOptions = $this->groupOptions;
        } else {
            $groupOptions = $this->groupEntity->getOptionsAsArray($group);
        }

        $newProductOptions = [];

        $i = $productOptions ? max(array_keys($productOptions)) + 1 : 1;

        foreach ($groupOptions as $groupOption) {
            $issetGroupOptionInProduct = false;

            foreach ($productOptions as $optionIndex => $productOption) {
                if (!empty($productOption['group_option_id'])
                    && $productOption['group_option_id'] == $groupOption['option_id']
                ) {
                    $issetGroupOptionInProduct = true;
                }
            }

            if (!$issetGroupOptionInProduct) {
                $groupOption['group_option_id'] = $groupOption['id'];

//                $groupOption['id'] = (string)$i;
                $groupOption['id'] = null; //override reason

                $groupOption['option_id'] = '0';

                $groupOption = $this->convertGroupToProductOptionValues($groupOption);
                $newProductOptions[$i] = $groupOption;
                $this->productGroupNewOptionIds[] = $groupOption['group_option_id'];
            }
            $i++;
        }

        return $productOptions + $newProductOptions;
    }

    /**
     * Modify options that were modified in group
     *
     * @param array $productOptions
     * @return array
     */
    protected function modifyOptionProcess(array $productOptions)
    {
        foreach ($productOptions as $productOptionId => $productOption) {
            $groupOptionId = !empty($productOption['group_option_id']) ? $productOption['group_option_id'] : null;
            if (!$groupOptionId) {
                continue;
            }
            if ($this->isOptionWereRecreated($groupOptionId)) {
                continue;
            }
            if (!empty($this->modifiedDownGroupOptions[$groupOptionId])) {
                foreach ($this->modifiedDownGroupOptions[$groupOptionId] as $modPropertyKey => $modProperty) {
                    if ($modPropertyKey == 'values' && is_array($modProperty)) {
                        /**
                         * @todo is corresponding product option another type? we must recreate it early maybe.
                         */
                        if (empty($productOptions[$productOptionId][$modPropertyKey])) {
                            $productOptions[$productOptionId][$modPropertyKey] = [];
                        }

                        foreach ($modProperty as $valueId => $valueData) {
                            //Option value were deleted in group - delete it in corresponding product option
                            if (!empty($valueData['option_type_id'])) {
                                $productOptions[$productOptionId][$modPropertyKey] =
                                    $this->markProductOptionValueAsDelete(
                                        $productOptions[$productOptionId][$modPropertyKey],
                                        $valueData['option_type_id'],
                                        'group_option_value_id'
                                    );
                            } else {
                                $productOptions[$productOptionId][$modPropertyKey] =
                                    $this->getModifyProductOptionValue(
                                        $productOptions[$productOptionId][$modPropertyKey],
                                        $valueId,
                                        $valueData
                                    );
                            }
                        }
                    } elseif (!is_array($modProperty)) {
                        unset($productOptions[$productOptionId][$modPropertyKey]);
                    }
                }
            }

            if (!empty($this->modifiedUpGroupOptions[$groupOptionId])) {
                foreach ($this->modifiedUpGroupOptions[$groupOptionId] as $modPropertyKey => $modProperty) {
                    if ($modPropertyKey == 'values' && is_array($modProperty)) {
                        /**
                         * @todo is corresponding product option another type? we must recreate it early maybe.
                         */
                        if (empty($productOptions[$productOptionId][$modPropertyKey])) {
                            $productOptions[$productOptionId][$modPropertyKey] = [];
                        }

                        foreach ($modProperty as $valueId => $valueData) {
                            if (!empty($valueData['option_type_id'])) {
                                $productOptions[$productOptionId][$modPropertyKey][] =
                                    $this->convertGroupOptionValueToProductOptionValue(
                                        $valueData,
                                        $productOptionId,
                                        $productOptions[$productOptionId][$modPropertyKey]
                                    );
                            } else {
                                $productOptions[$productOptionId][$modPropertyKey] =
                                    $this->getModifyProductOptionValue(
                                        $productOptions[$productOptionId][$modPropertyKey],
                                        $valueId,
                                        $valueData
                                    );
                            }
                        }
                    } elseif (!is_array($modProperty)) {
                        $productOptions[$productOptionId][$modPropertyKey] = $modProperty;
                    }
                }
            }

            if (!empty($this->addedProductValues[$groupOptionId])) {
                foreach ($this->addedProductValues[$groupOptionId] as $modPropertyKey => $modProperty) {
                    if ($modPropertyKey == 'values' && is_array($modProperty)) {
                        if (empty($productOptions[$productOptionId][$modPropertyKey])) {
                            continue;
                        }

                        foreach ($modProperty as $valueId => $valueData) {
                            //delete product option value that was added to template option
                            if (empty($valueData['option_type_id'])) {
                                continue;
                            }
                            $productOptions[$productOptionId][$modPropertyKey] =
                                $this->markProductOptionValueAsDelete(
                                    $productOptions[$productOptionId][$modPropertyKey],
                                    $valueData['option_type_id'],
                                    'option_type_id'
                                );
                        }
                    }
                }
            }

            //fix for existing group options that are lost in products
            if (isset($this->modifiedUpGroupOptions[$groupOptionId]['values']) && is_array($this->modifiedUpGroupOptions[$groupOptionId]['values'])) {
                if (empty($productOptions[$productOptionId]['values'])) {
                    $productOptions[$productOptionId]['values'] = [];
                }
                if (sizeof($this->modifiedUpGroupOptions[$groupOptionId]['values']) > sizeof($productOptions[$productOptionId]['values'])) {
                    foreach ($this->modifiedUpGroupOptions[$groupOptionId]['values'] as $valueId => $valueData) {
                        $valueExists = false;
                        foreach ($productOptions[$productOptionId]['values'] as $productOption) {
                            if ($productOption['mageworx_group_option_type_id'] == $valueData['mageworx_group_option_type_id']) {
                                $valueExists = true;
                                break;
                            }
                        }
                        if (!$valueExists) {
                            $valueData['option_type_id'] = $valueId;
                            $productOptions[$productOptionId]['values'][] =
                                $this->convertGroupOptionValueToProductOptionValue(
                                    $valueData,
                                    $productOptionId,
                                    $productOptions[$productOptionId]['values']
                                );
                        }
                    }
                }
            }

        }

        return $productOptions;
    }


    /**
     * Override reason:
     *  - $value['mageworx_optiontemplates_group_option_type_id']
     *  - $value['option_type_id'] = null;
     *
     * @param array $option
     * @return array
     */
    protected function convertGroupToProductOptionValues($option)
    {
        if (!empty($option['values'])) {
            foreach ($option['values'] as $valueKey => $value) {
                $value['group_option_value_id'] = $value['option_type_id'];

                //               $value['option_type_id'] = '-1';
                $value['mageworx_optiontemplates_group_option_type_id'] = $value['mageworx_group_option_type_id']; //override reason
                $value['option_type_id'] = null; //override reason

                $option['values'][$valueKey] = $value;
            }
        }

        return $option;
    }

    /**
     * Override reason: $groupOptionValueData['option_type_id'] = null
     * Convert group option value to product option value, keep changed attributes from config (qty, for example)
     *
     * @param array $groupOptionValueData
     * @param int $productOptionId
     * @param array $productOptionValues
     * @return string
     */
    protected function convertGroupOptionValueToProductOptionValue(array $groupOptionValueData, $productOptionId, $productOptionValues)
    {
        $groupOptionValueData['option_id'] = (string)$productOptionId;
        $groupOptionValueData['group_option_value_id'] = $groupOptionValueData['option_type_id'];

//        $groupOptionValueData['option_type_id'] = '-1';
        $groupOptionValueData['option_type_id'] = null; //override reason

        foreach ($productOptionValues as $optionValue) {
            if (empty($optionValue['group_option_value_id'])) {
                continue;
            }
            if (empty($this->oldGroupCustomOptionValues[$optionValue['group_option_value_id']])) {
                continue;
            }
            if (empty($this->oldGroupCustomOptionValues[$optionValue['group_option_value_id']]['mageworx_group_option_type_id'])) {
                continue;
            }
            $linkedMageworxOptionId = $this->oldGroupCustomOptionValues[$optionValue['group_option_value_id']]['mageworx_group_option_type_id'];
            if ($linkedMageworxOptionId != $groupOptionValueData['mageworx_group_option_type_id']) {
                continue;
            }
            foreach ($this->helper->getReapplyExceptionAttributeKeys() as $attribute) {
                if (!isset($optionValue[$attribute])) {
                    continue;
                }
                $oldOptionValueData = $this->oldGroupCustomOptionValues[$optionValue['group_option_value_id']][$attribute];
                if ($oldOptionValueData == $optionValue[$attribute]) {
                    continue;
                }
                $groupOptionValueData[$attribute] = $optionValue[$attribute];
            }
        }

        return $groupOptionValueData;
    }

    /**
     * Modify/add product option value properties by modified group option value properties
     *
     *
     * @param array $productOptionValueArray
     * @param int $groupOptionValueId
     * @param array $valueData
     * @return array
     */
    protected function getModifyProductOptionValue(array $productOptionValueArray, $groupOptionValueId, $valueData)
    {
        foreach ($productOptionValueArray as $optionValueKey => $optionValue) {
            if (!empty($optionValue['group_option_value_id']) &&
                $groupOptionValueId == $optionValue['group_option_value_id']
            ) {
                foreach ($valueData as $key => $value) {
                    $productOptionValueArray[$optionValueKey][$key] = $value;
                }

                //check if there are dependencies left, can't do this to all fields because of unexpected behavior
                if(!isset($valueData['field_hidden_dependency'])){
                    $productOptionValueArray[$optionValueKey]['field_hidden_dependency'] = null;
                }
                break;
            }
        }

        return $productOptionValueArray;
    }

}
