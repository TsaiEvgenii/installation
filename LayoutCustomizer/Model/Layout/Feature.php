<?php
namespace BelVG\LayoutCustomizer\Model\Layout;

use Magento\Framework\DataObject;
use BelVG\LayoutCustomizer\Model\Layout\Feature\Parameter;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature
    as FeatureResource;

class Feature extends \Magento\Framework\Model\AbstractModel
{
    protected $params;
    protected $parameters = [];

    protected function _construct()
    {
        $this->params = new DataObject();
        $this->_init(FeatureResource::class);
    }

    public function getParams()
    {
        return $this->params->getData();
    }

    public function setParams(array $params)
    {
        $this->params->setData($params);
        return $this;
    }

    public function setParam($name, $value)
    {
        $this->params->setData($name, $value);
        return $this;
    }

    public function addParameter(Parameter $parameter)
    {
        $this->parameters[] = $parameter;
        return $this;
    }

    public function toArray(array $keys = [])
    {
        $data = parent::toArray();

        $data['_type'] = 'feature';
        $data['_subtype'] = $this->getType();

        if (empty($keys) || in_array('params', $keys)) {
            $data['params'] = $this->params->toArray();
        }

        if (empty($keys) || in_array('parameters', $keys)) {
            $data['parameters'] = array_map(function($parameter) {
                return $parameter->toArray();
            }, $this->parameters);
        }

        return $data;
    }
}
