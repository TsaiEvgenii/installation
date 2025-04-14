<?php
namespace BelVG\LayoutCustomizer\Ui\DataProvider\Layout\Form\Modifier;

use Magento\Framework\Registry;
use Magento\Ui\Component;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;
use BelVG\LayoutCustomizer\Helper\Layout\Assets as AssetHelper;
use BelVG\LayoutCustomizer\Helper\Layout\Block as LayoutBlockHelper;
use BelVG\LayoutCustomizer\Helper\Layout\ExtOptions as ExtOptionsHelper;
use BelVG\LayoutCustomizer\Helper\Layout\ExtParams as ExtParamsHelper;
use Magento\Framework\Serialize\SerializerInterface;

class BlockJson extends AbstractModifier
{
    protected $assetHelper;
    protected $layoutBlockHelper;
    protected $extOptionsHelper;
    protected $extParamsHelper;
    protected SerializerInterface $serializer;

    public function __construct(
        Registry $coreRegistry,
        AssetHelper $assetHelper,
        LayoutBlockHelper $layoutBlockHelper,
        ExtOptionsHelper $extOptionsHelper,
        ExtParamsHelper $extParamsHelper,
        SerializerInterface $serializer
    ) {
        parent::__construct($coreRegistry);
        $this->assetHelper = $assetHelper;
        $this->layoutBlockHelper = $layoutBlockHelper;
        $this->extOptionsHelper = $extOptionsHelper;
        $this->extParamsHelper = $extParamsHelper;
        $this->serializer = $serializer;
    }

    public function modifyData(array $data)
    {
        $model = $this->getModel();
        if ($model->getId() && isset($data[$model->getId()])) {
            $data[$model->getId()]['block_json'] = $this->getBlockJson();
        }
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        // Add layout preview
        $meta['preview'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'fieldset',
                        'collapsible'   => true,
                        'opened'        => false,
                        'label'         => __('Preview'),
                        'sortOrder' => 20
                    ]]],
            'children' => [
                'layout_preview' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component'     => 'BelVG_LayoutCustomizer/js/layout-form/component/preview',
                                'dataScope'     => 'block_json',
                                'previewConfig' => [],
                                'previewAssets' => $this->getAssets(),
                                'dataConfig'    => $this->getDataConfig()
                            ]]]],
                'block_json' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType'      => Text::NAME,
                                'formElement'   => Textarea::NAME,
                                'componentType' => Field::NAME,
                                'dataScope'     => 'block_json',
                                'visible'       => false
                            ]]]]
            ]
        ];

        // Add editor modal
        $meta['editor_modal'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'component'     => 'BelVG_LayoutCustomizer/js/layout-form/component/editor/modal',
                        'provider'      => 'belvg_layoutcustomizer_layout_form.layout_form_data_source',
                        'isTemplate'    => false,
                        'options'       => ['title' => __('Edit Layout Blocks')],
                        'onCancel'      => 'actionCancel'
                    ]]],
            'children' => [
                'editor' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'        => Container::NAME,
                                'component'            => 'BelVG_LayoutCustomizer/js/layout-form/component/editor',
                                'dataScope'            => 'block_json',
                                'editorConfig'         => $this->getEditorConfig(),
                                'editorAssets'         => $this->getAssets(),
                                'dataConfig'           => $this->getDataConfig(),
                                'overallWidthScope'    => 'width',
                                'overallHeightScope'   => 'height',
                                'overallWidthParamId'  => $this->getOverallWidthParamId(),
                                'overallHeightParamId' => $this->getOverallHeightParamId()
                            ]]]]]];
        return $meta;
    }

    protected function getBlockJson()
    {
        return $this->serializer->serialize($this->getBlockData());
    }

    protected function getBlockData()
    {
        $model = $this->getModel();
        return $model->getId()
            ? $this->layoutBlockHelper->load($model->getId())
            : [];
    }

    protected function getEditorConfig()
    {
        $storeId = $this->getModel()->getStoreId();
        return [
            'ExtOptions' => $this->extOptionsHelper->getOptionTree($storeId),
            'ExtParams'  => $this->extParamsHelper->getOptionTree($storeId)
        ];
    }

    protected function getAssets()
    {
        return $this->assetHelper->getAssets();
    }

    protected function getDataConfig()
    {
        return [];
    }

    protected function getOverallWidthParamId()
    {
        return $this->extParamsHelper->getOverallWidthParamId();
    }

    protected function getOverallHeightParamId()
    {
        return $this->extParamsHelper->getOverallHeightParamId();
    }
}
