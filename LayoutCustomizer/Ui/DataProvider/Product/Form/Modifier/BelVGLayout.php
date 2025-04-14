<?php


namespace BelVG\LayoutCustomizer\Ui\DataProvider\Product\Form\Modifier;

use BelVG\LayoutCustomizer\Helper\Data as LayoutHelper;
use Magento\Ui\Component\Form\Field;

class BelVGLayout extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    protected $locator;
    protected $urlBuilder;

    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        if ($name = $this->getGeneralPanelName($meta)) {
            $meta[$name]['children'][LayoutHelper::PRODUCT_LAYOUT_ATTR]['arguments']['data']['config']  = [
                'component' => 'Magento_Catalog/js/components/attribute-set-select',
                'disableLabel' => true,
                'filterOptions' => true,
                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                'formElement' => 'select',
                'componentType' => Field::NAME,
                'visible' => 1,
                'source' => $name,
                'dataScope' => LayoutHelper::PRODUCT_LAYOUT_ATTR,
                'multiple' => false,
                'disabled' => $this->locator->getProduct()->isLockedAttribute(LayoutHelper::PRODUCT_LAYOUT_ATTR),
            ];
        }

        return $meta;
    }

}
