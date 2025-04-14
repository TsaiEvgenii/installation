<?php


namespace BelVG\LayoutCustomizer\ViewModel;


class Config implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $configBlock;

    public function __construct(
        \BelVG\LayoutCustomizer\Block\Config $blockConfig
    )
    {
        $this->configBlock = $blockConfig;
    }

    public function getConfigBlock()
    {
        return $this->configBlock;
    }
}