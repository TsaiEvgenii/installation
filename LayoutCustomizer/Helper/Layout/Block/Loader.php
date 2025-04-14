<?php
namespace BelVG\LayoutCustomizer\Helper\Layout\Block;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\CollectionFactory
    as BlockCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\CollectionFactory
    as FeatureCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement\CollectionFactory
    as MeasurementCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter\CollectionFactory
    as BlockParameterCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter\CollectionFactory
    as FeatureParameterCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Restriction\CollectionFactory
    as BlockRestrictionCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction\CollectionFactory
    as BlockMeasurementRestrictionCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Link\CollectionFactory
    as BlockLinkCollectionFactory;

class Loader
{
    protected $blockCollectionFactory;
    protected $featureCollectionFactory;
    protected $measurementCollectionFactory;
    protected $blockParameterCollectionFactory;
    protected $featureParameterCollectionFactory;
    protected $blockRestrictionCollectionFactory;
    protected $blockMeasurementRestrictionCollectionFactory;
    protected $blockLinkCollectionFactory;

    public function __construct(
        BlockCollectionFactory $blockCollectionFactory,
        FeatureCollectionFactory $featureCollectionFactory,
        MeasurementCollectionFactory $measurementCollectionFactory,
        BlockParameterCollectionFactory $blockParameterCollectionFactory,
        FeatureParameterCollectionFactory $featureParameterCollectionFactory,
        BlockRestrictionCollectionFactory $blockRestrictionCollectionFactory,
        BlockMeasurementRestrictionCollectionFactory $blockMeasurementRestrictionCollectionFactory,
        BlockLinkCollectionFactory $blockLinkCollectionFactory
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->featureCollectionFactory = $featureCollectionFactory;
        $this->measurementCollectionFactory = $measurementCollectionFactory;
        $this->blockParameterCollectionFactory = $blockParameterCollectionFactory;
        $this->featureParameterCollectionFactory = $featureParameterCollectionFactory;
        $this->blockRestrictionCollectionFactory = $blockRestrictionCollectionFactory;
        $this->blockMeasurementRestrictionCollectionFactory = $blockMeasurementRestrictionCollectionFactory;
        $this->blockLinkCollectionFactory = $blockLinkCollectionFactory;
    }

    public function load($layoutId)
    {
        // block
        $blockCollection = $this->blockCollectionFactory
            ->create()
            ->addLayoutFilter($layoutId)
            ->setOrder('sort_order', 'asc');
        $blockIds = $blockCollection->getAllIds();
        if (!$blockIds) {
            return [];
        }

        // feature
        $featureCollection = $this->featureCollectionFactory
            ->create()
            ->addBlockFilter($blockIds)
            ->setOrder('sort_order', 'asc');
        $featureIds = $featureCollection->getAllIds();

        // feature parameters
        $featureParameterCollection = null;
        if (!empty($featureIds)) {
            $featureParameterCollection = $this->featureParameterCollectionFactory
                ->create()
                ->addFeatureFilter($featureIds)
                ->setOrder('sort_order', 'asc');
        }

        // measurement
        $measurementCollection = $this->measurementCollectionFactory
            ->create()
            ->addBlockFilter($blockIds)
            ->setOrder('sort_order', 'asc');

        // block parameters
        $blockParameterCollection = $this->blockParameterCollectionFactory
            ->create()
            ->addBlockFilter($blockIds)
            ->setOrder('sort_order', 'asc');

        // block restrictions
        $blockRestrictionCollection = $this->blockRestrictionCollectionFactory
            ->create()
            ->addBlockFilter($blockIds)
            ->setOrder('sort_order', 'asc');

        // block MeasurementRestrictions
        $blockMeasurementRestrictionCollection = $this->blockMeasurementRestrictionCollectionFactory
            ->create()
            ->addBlockFilter($blockIds)
            ->setOrder('sort_order', 'asc');

        // block links
        $blockLinkCollection = $this->blockLinkCollectionFactory
            ->create()
            ->addBlockFilter($blockIds)
            ->setOrder('sort_order', 'asc');

        // Assign children
        foreach ($blockCollection as $block) {
            $parentId = $block->getParentId();
            if ($parentId && ($parent = $blockCollection->getItemById($parentId))) {
                $parent->addChild($block);
            }
        }

        // Assign features
        foreach ($featureCollection as $feature) {
            $blockId = $feature->getBlockId();
            if ($blockId && ($block = $blockCollection->getItemById($blockId))) {
                $block->addFeature($feature);
            }
        }

        // Assign feature parameters
        if ($featureParameterCollection) {
            foreach ($featureParameterCollection as $parameter) {
                $featureId = $parameter->getFeatureId();
                if ($featureId && ($feature = $featureCollection->getItemById($featureId))) {
                    $feature->addParameter($parameter);
                }
            }
        }

        // Assign measurements
        foreach ($measurementCollection as $measurement) {
            $blockId = $measurement->getBlockId();
            if ($blockId && ($block = $blockCollection->getItemById($blockId))) {
                $block->addMeasurement($measurement);
            }
        }

        // Assign block parameters
        foreach ($blockParameterCollection as $parameter) {
            $blockId = $parameter->getBlockId();
            if ($blockId && ($block = $blockCollection->getItemById($blockId))) {
                $block->addParameter($parameter);
            }
        }

        // Assign block restrictions
        foreach ($blockRestrictionCollection as $restriction) {
            $blockId = $restriction->getBlockId();
            if ($blockId && ($block = $blockCollection->getItemById($blockId))) {
                $block->addRestriction($restriction);
            }
        }

        // Assign block restrictions
        foreach ($blockMeasurementRestrictionCollection as $measurementRestriction) {
            $blockId = $measurementRestriction->getBlockId();
            if ($blockId && ($block = $blockCollection->getItemById($blockId))) {
                $block->addMeasurementRestriction($measurementRestriction);
            }
        }

        // Assign block links
        foreach ($blockLinkCollection as $link) {
            $blockId = $link->getBlockId();
            if ($blockId && ($block = $blockCollection->getItemById($blockId))) {
                $block->addLink($link);
            }
        }

        $rootBlocks = array_filter(
            array_values($blockCollection->getItems()),
            function($block) {
                return $block->isRoot();
            });

        return array_map(function($block) {
            return $block->toArray();
        }, $rootBlocks);
    }
}
