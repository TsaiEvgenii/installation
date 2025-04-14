<?php
namespace BelVG\LayoutCustomizer\Model\Layout;

use Magento\Framework\DataObject;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block as BlockResource;
use BelVG\LayoutCustomizer\Model\Layout\Feature;
use BelVG\LayoutCustomizer\Model\Layout\Measurement;
use BelVG\LayoutCustomizer\Model\Layout\Block\Parameter;
use BelVG\LayoutCustomizer\Model\Layout\Block\Restriction;
use BelVG\LayoutCustomizer\Model\Layout\Block\MeasurementRestriction;
use BelVG\LayoutCustomizer\Model\Layout\Block\Link;

class Block extends \Magento\Framework\Model\AbstractModel
{
    protected $shapeParams;
    protected $children = [];
    protected $features = [];
    protected $measurements = [];
    protected $parameters = [];
    protected $restrictions = [];
    protected $measurementRestrictions = [];
    protected $links = [];

    protected function _construct()
    {
        $this->shapeParams = new DataObject();
        $this->_init(BlockResource::class);
    }

    public function isRoot()
    {
        return !$this->getParentId();
    }

    public function getShapeParams()
    {
        return $this->shapeParams->getData();
    }

    public function setShapeParams(array $params)
    {
        $this->shapeParams->setData($params);
        return $this;
    }

    public function setShapeParam($name, $value)
    {
        $this->shapeParams->setData($name, $value);
        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(Block $child)
    {
        $this->children[] = $child;
        return $this;
    }

    public function getFeatures()
    {
        return $this->features;
    }

    public function addFeature(Feature $feature)
    {
        $this->features[] = $feature;
        return $this;
    }

    public function getMeasurements()
    {
        return $this->measurements;
    }

    public function addMeasurement(Measurement $measurement)
    {
        $this->measurements[] = $measurement;
        return $this;
    }

    public function addParameter(Parameter $parameter)
    {
        $this->parameters[] = $parameter;
        return $this;
    }

    public function addRestriction(Restriction $restriction)
    {
        $this->restrictions[] = $restriction;
        return $this;
    }

    public function addMeasurementRestriction(MeasurementRestriction $measurementRestriction)
    {
        $this->measurementRestrictions[] = $measurementRestriction;
        return $this;
    }

    public function addLink(Link $link)
    {
        $this->links[] = $link;
        return $this;
    }

    public function toArray(array $keys = [])
    {
        $data = parent::toArray($keys);

        $data['_type'] = 'block';
        $data['_subtype'] = null;
        $data['_uid'] = sprintf('uid:block:%d', $this->getId());

        if (empty($keys) || in_array('shape_params', $keys)) {
            $data['shape_params'] = $this->shapeParams->toArray();
        }

        $children = [
            'children'     => $this->children,
            'features'     => $this->features,
            'measurements' => $this->measurements,
            'parameters'   => $this->parameters,
            'restrictions' => $this->restrictions,
            'measurement_restrictions' => $this->measurementRestrictions,
            'links'        => $this->links
        ];
        foreach ($children as $key => $list) {
            if (empty($keys) || in_array($key, $keys)) {
                $data[$key] = array_map(function($child) {
                    return $child->toArray();
                }, $list);
            }
        }
        return $data;
    }
}
