<?php
namespace BelVG\LayoutCustomizer\Ui\Component\Listing;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class LayoutColumns extends \Magento\Ui\Component\Listing\Columns
{
    protected $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $components, $data);
    }

    public function prepare()
    {
        parent::prepare();

        $storeId = $this->storeManager->getStore()->getId();
        if ($storeId != 0) {
            $config = $this->getData('config');
            $config['editorConfig']['clientConfig']['saveUrl'] .= sprintf('store/%d/', $storeId);
            $this->setData('config', $config);
        }
    }
}
