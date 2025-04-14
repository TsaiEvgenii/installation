<?php
namespace BelVG\LayoutCustomizer\Helper\Layout;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Psr\Log\LoggerInterface;

class Assets
{
    protected $assetRepository;
    protected $request;
    protected $logger;

    protected $assets;

    public function __construct(
        AssetRepository $assetRepository,
        RequestInterface $request,
        LoggerInterface $logger)
    {
        $this->assetRepository = $assetRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function getAssets()
    {
        if (is_null($this->assets)) {
            $this->assets = $this->doGetAssets();
        }
        return $this->assets;
    }

    protected function doGetAssets()
    {
        // TODO: move to config
        $assets = [
            'primary-door-image' => 'BelVG_LayoutCustomizer::images/layout/primary-door.svg',
            'top-guided-w-fire-escape' => 'BelVG_LayoutCustomizer::images/layout/top-guided-w-fire-escape.svg'
        ];
        return array_map([$this, 'getAssetUrl'], $assets);
    }

    // @see Magento\Framework\View\Element\AbstractBlock::getViewFileUrl()
    protected function getAssetUrl($fileId)
    {
        $url = false;
        try {
            $params = ['_secure' => $this->request->isSecure()];
            $url = $this->assetRepository->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
        }
        return $url;
    }
}
