<?php
namespace BelVG\LayoutCustomizer\Ui\DataProvider\Layout\LayoutDataProvider\Modifier;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

// @see vendor/magento/module-catalog/Ui/DataProvider/Product/Form/Modifier/Related.php

class LayoutSelect implements ModifierInterface
{
    public function __construct(
        protected readonly RequestInterface $request,
        protected readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $copyUrl = $this->urlBuilder->getUrl(
            'belvg_layoutcustomizer/layout/copyBlocks',
            ['store' => $this->request->getParam('store')]);

        return array_merge($meta, [
            'layout_select' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Modal::NAME,
                            'component' => 'BelVG_LayoutCustomizer/js/layout/layout-select',
                            'options' => [
                                'title' => __('Select Target Layouts'),
                                'copyUrl' => $copyUrl,
                                'buttons' => [
                                    [
                                        'text' => __('Copy Layout Drawing'),
                                        'class' => 'action-primary',
                                        'actions' => [[
                                            'targetName' => 'belvg_layoutcustomizer_layout_listing.belvg_layoutcustomizer_layout_listing.layout_select',
                                            'actionName' => 'copy'
                                        ]]
                                    ]
                                ]]]]],
                'children' => [
                    'layout_grid' => $this->getGrid(),
                    'layout_listing' => $this->getProvider()
                ]]]);
    }

    protected function getGrid()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => true,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => 'belvg_layoutcustomizer_layout_select_listing',
                        'map' => [
                            'id' => 'layout_id',
                            'identifier' => 'identifier'
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ]]]],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => ''
                            ]]],
                    'children' => $this->getRecordColumns()
                ]]];
    }

    protected function getProvider()
    {
        $listing = 'belvg_layoutcustomizer_layout_select_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => $listing,
                        'externalProvider' => $listing . '.' . $listing . '_data_source',
                        'selectionsProvider' => $listing . '.' . $listing . '.belvg_layoutcustomizer_layout_select_columns.ids',
                        'ns' => $listing,
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [],
                        'exports' => []
                    ]]]];
    }

    protected function getRecordColumns()
    {
        return [
            'id'         => $this->getTextColumn('id', false, __('ID'), 10),
            'identifier' => $this->getTextColumn('identifier', false, __('Identifier'), 20)
        ];
    }

    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ]]]];

        return $column;
    }
}
