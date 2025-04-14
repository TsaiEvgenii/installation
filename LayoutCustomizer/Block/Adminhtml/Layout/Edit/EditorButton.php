<?php
namespace BelVG\LayoutCustomizer\Block\Adminhtml\Layout\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class EditorButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => 'Editor',
            'class' => 'action-secondary',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'belvg_layoutcustomizer_layout_form.belvg_layoutcustomizer_layout_form.editor_modal',
                                'actionName' => 'toggleModal'
                            ]
                        ]
                    ]
                ]
            ],
            'on_click' => '',
            'sort_order' => 20
        ];
    }
}
