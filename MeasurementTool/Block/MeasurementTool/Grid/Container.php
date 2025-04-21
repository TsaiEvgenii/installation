<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block\MeasurementTool\Grid;


use BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

class Container extends Template
{

    /** @var MeasurementToolInterface */
    private MeasurementToolInterface $measurementTool;

    public function setMeasurementTool(MeasurementToolInterface $measurementTool): static
    {
        $this->measurementTool = $measurementTool;
        return $this;
    }
    private function getMeasurementTool(): MeasurementToolInterface
    {
        return $this->measurementTool;
    }

    /**
     * @throws LocalizedException
     */
    public function getChildHtml($alias = '', $useCache = false): string
    {
        $layout = $this->getLayout();
        if ($layout) {
            $name = $this->getNameInLayout();
            foreach ($layout->getChildBlocks($name) as $child) {
                $child->setMeasurementTool($this->getMeasurementTool());
            }
        }
        return parent::getChildHtml($alias, $useCache);
    }
}