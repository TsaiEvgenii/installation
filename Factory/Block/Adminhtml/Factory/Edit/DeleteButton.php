<?php
namespace BelVG\Factory\Block\Adminhtml\Factory\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        if ($this->getModelId()) {
            $data = [
                'label' => __('Delete Factory'),
                'class' => 'delete',
                'on_click' => sprintf(
                    'deleteConfirm(\'%s\', \'%s\')',
                    __('Are you sure?'), $this->getDeleteUrl()),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [
            'factory_id' => $this->getModelId(),
            'store'      => $this->getStore()
        ]);
    }
}
