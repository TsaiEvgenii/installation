<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Config\FactoryMaterialDeliveryTypeOptions;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use BelVG\Factory\Api\Data\FactoryMaterialDeliveryTypeOptionsInterface;
use BelVG\Factory\Model\Config\FactoryMaterialDeliveryType\CategoryFactoryMaterialDeliveryType;

/**
 * Class CategoryTree
 *
 * @package BelVG\Factory\Model\Config\FactoryMaterialDeliveryTypeOptions
 */
class CategoryTree implements FactoryMaterialDeliveryTypeOptionsInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * CategoryTree constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray($storeId = 0): array
    {
        return $this->getTree($this->getShownIds(), $storeId);
    }

    /**
     * @param $shownIds
     * @param $storeId
     * @return array
     * @throws LocalizedException
     */
    protected function getTree($shownIds, $storeId): array
    {
        $collection = $this->collectionFactory
            ->create()
            ->addAttributeToFilter('entity_id', ['in' => $shownIds])
            ->addAttributeToSelect(['name', 'is_active', 'parent_id'])
            ->setStoreId($storeId);

        $categoryById = [
            CategoryModel::TREE_ROOT_ID => [
                'label' => '',
                'optgroup' => [[
                    'value' => 0,
                    'label' => __('Any Category'),
                    'is_active' => true,
                    '__disableTmpl' => true,
                    'type' => CategoryFactoryMaterialDeliveryType::CATEGORY_COLOUR
                ]],
            ],
        ];
        foreach ($collection as $category) {
            $id = $category->getId();
            $parentId = $category->getParentId();

            isset($categoryById[$id])
            or $categoryById[$id] = ['value' => $id];
            $categoryById[$id]['is_active'] = $category->getIsActive();
            $categoryById[$id]['label'] = $category->getName();
            $categoryById[$id]['__disableTmpl'] = true;
            $categoryById[$id]['type'] = CategoryFactoryMaterialDeliveryType::CATEGORY_COLOUR;

            isset($categoryById[$parentId])
            or $categoryById[$parentId] = ['value' => $parentId];
            $categoryById[$parentId]['optgroup'][] = &$categoryById[$id];
        }

        return $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function getShownIds(): array
    {
        $collection = $this->collectionFactory
            ->create()
            ->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID]);
        $ids = [];
        foreach ($collection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $ids[$parentId] = true;
            }
        }
        return array_keys($ids);
    }
}
