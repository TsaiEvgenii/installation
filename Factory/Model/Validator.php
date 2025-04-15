<?php
namespace BelVG\Factory\Model;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\Data\FactoryMaterialInterface;
use BelVG\Factory\Api\FactoryMaterialRepositoryInterface;
use BelVG\InsideOutsideColorPrice\Model\Config\AffectingDeliveryTimeParameters\InsideOutsideColor as ColorSource;
use BelVG\LayoutMaterial\Model\Config\Source\Material as MaterialSource;
use Magento\Store\Api\Data\StoreInterface;

class Validator
{
    const ERRORS_PER_MATERIAL = 5;

    protected $factoryMaterialRepo;
    protected $materialSource;
    protected $categorySource;
    protected $colorSource;

    // [materialId => materialLabel]
    protected $materialLabels;

    public function __construct(
        FactoryMaterialRepositoryInterface $factoryMaterialRepo,
        MaterialSource $materialSource,
        Validator\CategorySource $categorySource,
        ColorSource $colorSource
    )
    {
        $this->factoryMaterialRepo = $factoryMaterialRepo;
        $this->materialSource = $materialSource;
        $this->categorySource = $categorySource;
        $this->colorSource = $colorSource;
    }

    public function validate(FactoryInterface $factory, StoreInterface $store)
    {
        $errors = [];
        $factoryMaterials = $this->factoryMaterialRepo
            ->getListByFactory($factory)
            ->getItems();

        $errors = array_merge(
            $errors,
            $this->validateMaterials($factoryMaterials));

        if ($store->getId() != 0) {
            $errors = array_merge(
                $errors,
                $this->validateDeliveryRules($factoryMaterials, $store));
        }

        return $errors;
    }

    protected function validateDeliveryRules(
        array $factoryMaterials,
        StoreInterface $store)
    {
        $errors = [];
        foreach ($factoryMaterials as $factoryMaterial) {
            $materialErrors = $this->validateFactoryMaterialDeliveryRules($factoryMaterial, $store);
            if (!empty($materialErrors)) {
                $errors[] = __(
                    'Cannot handle following cases for material "%1": %2',
                    $this->getMaterialLabel($factoryMaterial->getMaterialId()),
                    implode(", ", $materialErrors));
            }
        }
        return $errors;
    }

    protected function validateFactoryMaterialDeliveryRules(
        FactoryMaterialInterface $factoryMaterial,
        StoreInterface $store)
    {
        $errors = [];

        $colors = $this->colorSource->toOptionHash();
        $categories = $this->categorySource->getStoreCategories($store);

        $hasCatchAll = false;
        // ['categoryId-colors' => true]
        $categoryColorsKeys = [];
        foreach ($factoryMaterial->getDeliveryRules() as $deliveryRule) {
            if  (!$deliveryRule->getCategoryId() && !$deliveryRule->getColors()) {
                // Rule allows for all categories and colors
                $hasCatchAll = true;
                break;

            } elseif (!$deliveryRule->getCategoryId()) {
                // Allows any category for specific color
                unset($colors[$deliveryRule->getColors()]);

            } elseif (!$deliveryRule->getColors()) {
                // Allows any colors for specific category
                unset($categories[$deliveryRule->getCategoryId()]);

            } else {
                // Add category/colors pair to list
                $key = $this->categoryColorsKey(
                    $deliveryRule->getCategoryId(),
                    $deliveryRule->getColors());
                $categoryColorsKeys[$key] = true;
            }
        }
        if ($hasCatchAll) return $errors;

        // Check all category/colors pairs
        foreach ($categories as $categoryId => $categoryPath) {
            foreach ($colors as $colorsId => $colorsLabel) {
                $key = $this->categoryColorsKey($categoryId, $colorsId);
                if (!isset($categoryColorsKeys[$key])) {
                    if (count($errors) <= self::ERRORS_PER_MATERIAL) {
                        $errors[] = __(
                            'category ID:%1 "%2" with colors "%3"',
                            $categoryId, $categoryPath, $colorsLabel);
                    } else {
                        $errors[] = __('(remaining cases omitted)');
                        break 2;
                    }
                }
            }
        }
        return $errors;
    }

    protected function validateMaterials(array $factoryMaterials)
    {
        // Get material IDs
        $usedMaterialIds = $usedMaterialIds = array_map(
            function($factoryMaterial) {
                return $factoryMaterial->getMaterialId();
            },
            $factoryMaterials);
        // [material_id => true]
        $usedMaterialIdsTable = array_fill_keys($usedMaterialIds, true);

        // Get unused material options
        $unusedMaterialOptions = array_filter(
            $this->materialSource->toOptionArray(),
            function($option) use ($usedMaterialIdsTable) {
                $value = $option['value'] ?? null;
                return $value && !isset($usedMaterialIdsTable[$value]);
            });

        $errors = [];
        if (!empty($unusedMaterialOptions)) {
            // "material1, material2, ..."
            $unusedMaterialsStr = implode(
                ', ',
                array_map(
                    function($option) {
                        return $option['label'];
                    },
                    $unusedMaterialOptions));
            $errors[] = __('Unused materials: %1', $unusedMaterialsStr);
        }
        return $errors;
    }

    protected function getMaterialLabel($materialId)
    {
        if (is_null($this->materialLabels)) {
            $this->materialLabels = [];
            foreach ($this->materialSource->toOptionArray() as $option)
                $this->materialLabels[$option['value']] = $option['label'];
        }
        return $this->materialLabels[$materialId] ?? '';
    }

    protected function categoryColorsKey($categoryId, $colors)
    {
        return sprintf('%d-%s', $categoryId, $colors);
    }
}
