<?php
namespace BelVG\LayoutCustomizer\Helper\Layout\Block;

use BelVG\LayoutCustomizer\Model\Layout\BlockFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block as BlockResource;
use BelVG\LayoutCustomizer\Model\Layout\FeatureFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature as FeatureResource;
use BelVG\LayoutCustomizer\Model\Layout\MeasurementFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement as MeasurementResource;
use BelVG\LayoutCustomizer\Model\Layout\Block\ParameterFactory as BlockParameterFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter as BlockParameterResource;
use BelVG\LayoutCustomizer\Model\Layout\Feature\ParameterFactory as FeatureParameterFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter as FeatureParameterResource;
use BelVG\LayoutCustomizer\Model\Layout\Block\RestrictionFactory as BlockRestrictionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Restriction as BlockRestrictionResource;
use BelVG\LayoutCustomizer\Model\Layout\Block\MeasurementRestrictionFactory as BlockMeasurementRestrictionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction as BlockMeasurementRestrictionResource;
use BelVG\LayoutCustomizer\Model\Layout\Block\LinkFactory as BlockLinkFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Link as BlockLinkResource;

class Saver
{
    protected $blockResource;
    protected $blockFactory;
    protected $featureResource;
    protected $featureFactory;
    protected $measurementResource;
    protected $measurementFactory;
    protected $blockParameterResource;
    protected $blockParameterFactory;
    protected $featureParameterResource;
    protected $featureParameterFactory;
    protected $blockRestrictionResource;
    protected $blockRestrictionFactory;
    protected $blockMeasurementRestrictionResource;
    protected $blockMeasurementRestrictionFactory;
    protected $blockLinkResource;
    protected $blockLinkFactory;

    protected $oldIds = [];

    public function __construct(
        BlockResource $blockResource,
        BlockFactory $blockFactory,
        FeatureResource $featureResource,
        FeatureFactory $featureFactory,
        MeasurementResource $measurementResource,
        MeasurementFactory $measurementFactory,
        BlockParameterResource $blockParameterResource,
        BlockParameterFactory $blockParameterFactory,
        FeatureParameterResource $featureParameterResource,
        FeatureParameterFactory $featureParameterFactory,
        BlockRestrictionResource $blockRestrictionResource,
        BlockRestrictionFactory $blockRestrictionFactory,
        BlockMeasurementRestrictionResource $blockMeasurementRestrictionResource,
        BlockMeasurementRestrictionFactory $blockMeasurementRestrictionFactory,
        BlockLinkResource $blockLinkResource,
        BlockLinkFactory $blockLinkFactory)
    {
        $this->blockResource = $blockResource;
        $this->blockFactory = $blockFactory;
        $this->featureResource = $featureResource;
        $this->featureFactory = $featureFactory;
        $this->measurementResource = $measurementResource;
        $this->measurementFactory = $measurementFactory;
        $this->blockParameterResource = $blockParameterResource;
        $this->blockParameterFactory = $blockParameterFactory;
        $this->featureParameterResource = $featureParameterResource;
        $this->featureParameterFactory = $featureParameterFactory;
        $this->blockRestrictionResource = $blockRestrictionResource;
        $this->blockRestrictionFactory = $blockRestrictionFactory;
        $this->blockMeasurementRestrictionResource = $blockMeasurementRestrictionResource;
        $this->blockMeasurementRestrictionFactory = $blockMeasurementRestrictionFactory;
        $this->blockLinkResource = $blockLinkResource;
        $this->blockLinkFactory = $blockLinkFactory;
    }

    public function save($layoutId, array $blocks) {
        // Collect old object IDs
        // to avoid deleting moved objects
        $this->collectOldIds($blocks);
        $savedIds = [];
        // export UID -> persistent UID
        $uidMap = [];
        // references to update
        // [block => [linkId => refUid]]
        $linksToUpdate = ['block' => []];
        // Save object tree
        $this->saveBlocks($savedIds, $uidMap, $linksToUpdate, $layoutId, $blocks);
        // Remove old blocks
        $this->blockResource->deleteOther(
            $layoutId,
            array_merge($savedIds, $this->oldIds['block']));

        // Update links
        $this->updateBlockLinks($uidMap, $linksToUpdate['block']);
    }

    protected function collectOldIds(array $blocks)
    {
        $this->oldIds = [
            'block'             => [],
            'feature'           => [],
            'feature_parameter' => [],
            'measurement'       => [],
            'parameter'         => [],
            'restriction'       => [],
            'measurement_restriction'       => [],
            'link'              => []
        ];
        foreach ($blocks as $block) {
            $this->_collectOldIds($block);
        }
    }

    /**
        Collect old object persistent IDs to avoid deleting them
        in case their parent changed (via drag-and-drop in editor)
    */
    protected function _collectOldIds($block)
    {
        // Root block
        $this->addOldIds('block', [$block], 'block_id');

        // Features
        $features = $this->getArray($block, 'features');
        $this->addOldIds('feature', [$features], 'feature_id');
        // Feature parameters
        foreach ($features as $feature) {
            $parameters = $this->getArray($feature, 'parameters');
            $this->addOldIds('feature_parameter', [$parameters], 'parameter_id');
        }

        // Measurements
        $measurements = $this->getArray($block, 'measurements');
        $this->addOldIds('measurement', $measurements, 'measurement_id');

        // Parameters
        $parameters = $this->getArray($block, 'parameters');
        $this->addOldIds('parameter', $parameters, 'parameter_id');

        // Restrictions
        $restrictions = $this->getArray($block, 'restrictions');
        $this->addOldIds('restriction', $restrictions, 'restriction_id');

        // Measurement Restrictions
        $measurementRestrictions = $this->getArray($block, 'measurement_restrictions');
        $this->addOldIds('measurement_restriction', $measurementRestrictions, 'measurement_restriction_id');

        // Links
        $links = $this->getArray($block, 'links');
        $this->addOldIds('link', $links, 'link_id');

        $children = isset($block['children']) ? (array) $block['children'] : [];
        foreach ($children as $child) {
            $this->_collectOldIds($child);
        }
    }

    protected function addOldIds($type, $items, $idField)
    {
        $this->oldIds[$type] = array_merge(
            $this->oldIds[$type],
            $this->toIds($items, $idField));
    }

    protected function saveBlocks(
        array &$savedIds, array &$uidMap,
        array &$linksToUpdate,
        $layoutId, $blocks, $parentId = null)
    {
        $sortOrder = 0;
        foreach ($blocks as $block) {
            $block['sort_order'] = $sortOrder++;
            $this->saveBlock(
                $savedIds, $uidMap,
                $linksToUpdate,
                $layoutId, $block, $parentId);
        }
    }

    protected function saveBlock(
        array &$savedIds, array &$uidMap,
        array &$linksToUpdate,
        $layoutId, $data, $parentId)
    {
        $block = $this->blockFactory
            ->create()
            ->addData($data)
            ->setLayoutId($layoutId)
            ->setParentId($parentId)
            ->setShapeParams($this->getArray($data, 'shape_params'));
        $block->save();
        $savedIds[] = $block->getId();
        if (isset($data['_uid'])) {
            $uidMap[$data['_uid']] = $block->getId();
        }

        $this->saveFeatures(
            $uidMap, $block->getId(),
            $this->getArray($data, 'features'));

        $this->saveMeasurements(
            $block->getId(),
            $this->getArray($data, 'measurements'));

        $this->saveBlockParameters(
            $block->getId(),
            $this->getArray($data, 'parameters'));

        $this->saveBlockRestrictions(
            $block->getId(),
            $this->getArray($data, 'restrictions'));

        $this->saveBlockMeasurementRestrictions(
            $block->getId(),
            $this->getArray($data, 'measurement_restrictions'));

        $this->saveBlockLinks(
            $linksToUpdate,
            $uidMap,
            $block->getId(),
            $this->getArray($data, 'links'));

        $this->saveBlocks(
            $savedIds, $uidMap,
            $linksToUpdate,
            $layoutId,
            $this->getArray($data, 'children'),
            $block->getId());
    }

    // Features

    protected function saveFeatures(&$uidMap, $blockId, array $features)
    {
        $savedIds = [];
        $sortOrder = 0;
        foreach ($features as $feature) {
            $feature['sort_order'] = $sortOrder++;
            $savedIds[] = $this->saveFeature($uidMap, $blockId, $feature);
        }
        $this->featureResource->deleteOther(
            $blockId,
            array_merge($savedIds, $this->oldIds['feature']));
    }

    protected function saveFeature(&$uidMap, $blockId, array $data)
    {
        $feature = $this->featureFactory
            ->create()
            ->addData($data)
            ->setParams($this->getArray($data, 'params'))
            ->setBlockId($blockId);
        $feature->save();
        if (isset($data['_uid'])) {
            $uidMap[$data['_uid']] = $feature->getId();
        }
        $this->saveFeatureParameters($feature->getId(), $this->getArray($data, 'parameters'));
        return $feature->getId();
    }


    // Measurements

    protected function saveMeasurements($blockId, array $measurements)
    {
        $savedIds = [];
        $sortOrder = 0;
        foreach ($measurements as $measurement) {
            $measurement['sort_order'] = $sortOrder++;
            $savedIds[] = $this->saveMeasurement($blockId, $measurement);
        }
        $this->measurementResource->deleteOther(
            $blockId,
            array_merge($savedIds, $this->oldIds['measurement']));
    }

    protected function saveMeasurement($blockId, array $data)
    {
        $measurement = $this->measurementFactory
            ->create()
            ->addData($data)
            ->setParams($this->getArray($data, 'params'))
            ->setBlockId($blockId);
        $measurement->save();
        return $measurement->getId();
    }


    // Block parameters

    protected function saveBlockParameters($blockId, array $parameters)
    {
        $savedIds = [];
        $sortOrder = 0;
        foreach ($parameters as $parameter) {
            $parameter['sort_order'] = $sortOrder++;
            $savedIds[] = $this->saveBlockParameter($blockId, $parameter);
        }
        $this->blockParameterResource->deleteOther(
            $blockId,
            array_merge($savedIds, $this->oldIds['parameter']));
    }

    protected function saveBlockParameter($blockId, array $data)
    {
        $parameter = $this->blockParameterFactory
            ->create()
            ->addData($data)
            ->setData('options', $this->getArray($data, 'options'))
            ->setBlockId($blockId);
        $parameter->save();
        return $parameter->getId();
    }


    // Feature parameters

    protected function saveFeatureParameters($featureId, array $parameters)
    {
        $savedIds = [];
        $sortOrder = 0;
        foreach ($parameters as $parameter) {
            $parameter['sort_order'] = $sortOrder++;
            $savedIds[] = $this->saveFeatureParameter($featureId, $parameter);
        }
        $this->featureParameterResource->deleteOther(
            $featureId,
            array_merge($savedIds, $this->oldIds['feature_parameter']));
    }

    protected function saveFeatureParameter($featureId, $data)
    {
        $parameter = $this->featureParameterFactory
            ->create()
            ->addData($data)
            ->setData('options', $this->getArray($data, 'options'))
            ->setFeatureId($featureId);
        $parameter->save();
        return $parameter->getId();
    }


    // Block restrictions

    protected function saveBlockRestrictions($blockId, array $restrictions)
    {
        $savedIds = [];
        $sortOrder = 0;
        foreach ($restrictions as $restriction) {
            $restriction['sort_order'] = $sortOrder++;
            $savedIds[] = $this->saveBlockRestriction($blockId, $restriction);
        }
        $this->blockRestrictionResource->deleteOther(
            $blockId,
            array_merge($savedIds, $this->oldIds['restriction']));
    }

    protected function saveBlockRestriction($blockId, array $data)
    {
        $restriction = $this->blockRestrictionFactory
            ->create()
            ->addData($data)
            ->setParams($this->getArray($data, 'params'))
            ->setBlockId($blockId);
        $restriction->save();
        return $restriction->getId();
    }


    // Block MeasurementRestrictions

    protected function saveBlockMeasurementRestrictions($blockId, array $measurementRestrictions)
    {
        $savedIds = [];
        $sortOrder = 0;
        foreach ($measurementRestrictions as $measurementRestriction) {
            $measurementRestriction['sort_order'] = $sortOrder++;
            $savedIds[] = $this->saveBlockMeasurementRestriction($blockId, $measurementRestriction);
        }
        $this->blockMeasurementRestrictionResource->deleteOther(
            $blockId,
            array_merge($savedIds, $this->oldIds['measurement_restriction']));
    }

    protected function saveBlockMeasurementRestriction($blockId, array $data)
    {
        $measurementRestriction = $this->blockMeasurementRestrictionFactory
            ->create()
            ->addData($data)
            ->setParams($this->getArray($data, 'params'))
            ->setBlockId($blockId);
        $measurementRestriction->save();
        return $measurementRestriction->getId();
    }


    // Links

    protected function saveBlockLinks(&$linksToUpdate, $uidMap, $blockId, array $links)
    {
        $savedIds = [];
        $sortOrder = 0;
        foreach ($links as $link) {
            $link['sort_order'] = $sortOrder++;
            $savedIds[] = $this->saveBlockLink($linksToUpdate, $uidMap, $blockId, $link);
        }
        $this->blockLinkResource->deleteOther(
            $blockId,
            array_merge($savedIds, $this->oldIds['link']));
    }

    protected function saveBlockLink(&$linksToUpdate, $uidMap, $blockId, array $data)
    {
        $link = $this->blockLinkFactory
            ->create()
            ->addData($data)
            ->setBlockId($blockId);

        $refUid = null;
        $refId = null;
        if (!empty($data['ref'])) {
            $refUid = $data['ref'];
            if (isset($uidMap[$refUid])) {
                // reference block is already saved, store reference ID
                $refId = $uidMap[$refUid];
                $link->setRefId($refId);
            }
        }
        $link->save();

        if ($refUid && !$refId) {
            // store temporary reference UID
            $link->setRef($refUid);
            $linksToUpdate['block'][] = $link;
        }

        return $link->getId();
    }

    protected function updateBlockLinks($uidMap, $linksToUpdate)
    {
        $data = [];
        foreach ($linksToUpdate as $link) {
            if (!empty($uidMap[$link->getRef()])) {
                $refId = $uidMap[$link->getRef()];
                $link->setRefId($refId);
                $link->save();
            }
        }
    }

    // Helper methods

    protected function getArray(array $data, $field)
    {
        return isset($data[$field]) ? (array) $data[$field] : [];
    }

    protected function toIds(array $items, $idField)
    {
        return array_filter(
            array_map(function($item) use ($idField) {
                return isset($item[$idField]) ? $item[$idField] : null;
            }, $items));
    }
}
