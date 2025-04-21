<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block;


use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveMeasurementToolButton implements ButtonProviderInterface
{

    public function getButtonData(): array
    {
        return [
            'label' => __('Save Measurement tool'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['BelVG_MeasurementTool/js/button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
        ];
    }
}