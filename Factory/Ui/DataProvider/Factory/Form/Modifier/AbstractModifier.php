<?php
namespace BelVG\Factory\Ui\DataProvider\Factory\Form\Modifier;

use Magento\Framework\Registry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

abstract class AbstractModifier implements ModifierInterface
{
    protected $coreRegistry;

    public function __construct(Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    protected function getObject()
    {
        return $this->coreRegistry->registry('belvg_factory');
    }
}
