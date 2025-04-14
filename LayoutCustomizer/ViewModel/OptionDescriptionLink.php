<?php

namespace BelVG\LayoutCustomizer\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use BelVG\LayoutCustomizer\Helper\Data as Helper;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class OptionDescriptionLink implements ArgumentInterface
{
    public function __construct(
        private readonly StoreManager $storeManager,
        private readonly Helper $dataHelper
    ) {}

    public function getOptionDescriptionLink(): ?string
    {
        $store = $this->getCurrentStoreId();
        return $this->dataHelper->getOptionDescriptionLink($store);
    }

    private function getCurrentStoreId(): ?int
    {
        return $this->storeManager->getStore()->getId();
    }

}
