<?php
namespace BelVG\Factory\Block\Adminhtml\Factory\Edit;

use Magento\Backend\Block\Widget\Context;

abstract class GenericButton
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getModelId()
    {
        return $this->context->getRequest()->getParam('factory_id');
    }

    public function getStore()
    {
        return $this->context->getRequest()->getParam('store');
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
