<?php

namespace BelVG\MageWorxOptionServerSideRender\ViewModel;

use BelVG\MageWorxOptionServerSideRender\Model\Config\Image\Resizer as ImageManager;

class ImageHelper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public function __construct(
        private ImageManager $imageManager
    ) {
        $this->imageManager = $imageManager;
    }

    /**
     * @param iterable $imageData
     * @param int $width
     * @param int $height
     * @return string|null
     */
    public function getResizedThumbnailImg(
        iterable $imageData,
        int $width = 65,
        int $height = 50
    ) :?string {
        $result = $imageData['url'];
        if (isset($imageData['file'])) {
            $result = $this->imageManager->resize($imageData['file'], $width, $height);
        }

        return $result;
    }
}
