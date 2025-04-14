<?php
namespace BelVG\LayoutCustomizer\Block\Product\View;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context as ProductContext;
use BelVG\LayoutCustomizer\Helper\Layout\Assets as AssetHelper;

class Customizer extends AbstractProduct
{
    protected $assetHelper;

    public function __construct(
        AssetHelper $assetHelper,
        ProductContext $context,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->assetHelper = $assetHelper;
    }

    public function getCustomizerConfig()
    {
        return [
            'customizerAssets' => $this->assetHelper->getAssets()
        ];
    }
}
