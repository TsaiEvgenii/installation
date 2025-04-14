<?php
namespace BelVG\LayoutCustomizer\Ui\DataProvider\Layout\Form\Modifier;

use Magento\Framework\Registry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AbstractModifier implements ModifierInterface
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

    protected function getModel()
    {
        return $this->coreRegistry->registry('belvg_layoutcustomizer_layout');
    }
}
