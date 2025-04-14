<?php

namespace BelVG\LayoutCustomizer\Helper\Layout;

use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use Magento\Catalog\Model\Product;

class Block
{
    protected $saver;
    protected $loader;
    protected $layoutsCache = [];

    public function __construct(Block\Saver $saver, Block\Loader $loader)
    {
        $this->saver = $saver;
        $this->loader = $loader;
    }

    public function save($layoutId, array $data = [])
    {
        $this->saver->save($layoutId, $data);
    }

    public function load(
        $layoutId,
        bool $useCache = true
    ) {
        if ($useCache && isset($this->layoutsCache[$layoutId])) {
            return $this->layoutsCache[$layoutId];
        }

        $this->layoutsCache[$layoutId] = $this->loader->load($layoutId);

        return $this->layoutsCache[$layoutId];
    }

    public function stripIds(array $blocks)
    {
        $this->doStripIds($blocks);
        return $blocks;
    }

    public function getFinalDimensions(array &$block)
    {
        if ($block['measurements']) {
            foreach ($block['measurements'] as $measurementId => $measurement) {
                if (isset($measurement['params']['adjustment1']) && $measurement['params']['adjustment1']) {
                    $block[$measurement['type']] += $measurement['params']['adjustment1'];
                }
                if (isset($measurement['params']['adjustment2']) && $measurement['params']['adjustment2']) {
                    $block[$measurement['type']] += $measurement['params']['adjustment2'];
                }
            }
        }
        if ($block['children']) {
            foreach ($block['children'] as $childId => $child) {
                $this->getFinalDimensions($block['children'][$childId]);
            }
        }
    }

    /**
     * @param Product $product
     * @return array|int[]
     */
    public function getDefaultSizesForProduct(Product $product) :array {
        $layout = $product->getData(LayoutCustomizerHelper::PRODUCT_LAYOUT_ATTR);

        return $this->getDefaultSizesForLayout($layout);
    }

    /**
     * @param $layoutId
     * @return array|int[]
     */
    public function getDefaultSizesForLayout($layoutId) :array {
        $blocks = $this->load($layoutId);
        $width = 0;
        $height = 0;
        $blocksWrapperKey = array_key_first($blocks);
        if (isset($blocks[$blocksWrapperKey])) {
            $this->getFinalDimensions($blocks[$blocksWrapperKey]);
            $width = $blocks[$blocksWrapperKey]['width'];
            $height = $blocks[$blocksWrapperKey]['height'];
        }

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    protected function doStripIds(array &$blocks)
    {
        foreach ($blocks as &$block) {
            // block
            unset($block['layout_id']);
            unset($block['parent_id']);
            unset($block['block_id']);
            // features
            if (isset($block['features']) && is_array($block['features'])) {
                foreach ($block['features'] as &$feature) {
                    unset($feature['block_id']);
                    unset($feature['feature_id']);
                    // feature parameters
                    if (isset($feature['parameters']) && is_array($feature['parameters'])) {
                        foreach ($feature['parameters'] as &$parameter) {
                            unset($parameter['feature_id']);
                            unset($parameter['parameter_id']);
                        }
                    }
                }
            }
            // measurements
            if (isset($block['measurements']) && is_array($block['measurements'])) {
                foreach ($block['measurements'] as &$measurement) {
                    unset($measurement['block_id']);
                    unset($measurement['measurement_id']);
                }
            }
            // block parameters
            if (isset($block['parameters']) && is_array($block['parameters'])) {
                foreach ($block['parameters'] as &$parameter) {
                    unset($parameter['block_id']);
                    unset($parameter['parameter_id']);

                }
            }
            // block restrictions
            if (isset($block['restrictions']) && is_array($block['restrictions'])) {
                foreach ($block['restrictions'] as &$restriction) {
                    unset($restriction['block_id']);
                    unset($restriction['restriction_id']);
                }
            }
            // block measurement-restrictions
            if (isset($block['measurement_restrictions']) && is_array($block['measurement_restrictions'])) {
                foreach ($block['measurement_restrictions'] as &$restriction) {
                    unset($restriction['block_id']);
                    unset($restriction['measurement_restriction_id']);
                }
            }
            // block links
            if (isset($block['links']) && is_array($block['links'])) {
                foreach ($block['links'] as &$link) {
                    unset($link['block_id']);
                    unset($link['link_id']);
                }
            }
            // children
            if (isset($block['children']) && is_array($block['children'])) {
                $this->doStripIds($block['children']);
            }
        }
    }
}
