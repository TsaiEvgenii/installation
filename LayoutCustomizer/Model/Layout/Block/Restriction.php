<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Block;

use Magento\Framework\DataObject;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Restriction
    as RestrictionResource;

class Restriction extends \Magento\Framework\Model\AbstractModel
{
    protected $params;

    protected function _construct()
    {
        $this->params = new DataObject();
        $this->_init(RestrictionResource::class);
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

    public function toArray(array $keys = []) {
        $data = parent::toArray();

        $data['_type'] = 'restriction';
        $data['_subtype'] = 'block';

        if (empty($keys) || in_array('params', $keys)) {
            $data['params'] = $this->params->toArray();
        }
        return $data;
    }
}
