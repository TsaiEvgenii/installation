<?php

namespace BelVG\MageWorxOptionServerSideRender\Model\Config\Image;

use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Image\AdapterFactory;

class Resizer
{
    const MAGEWORX_OPTION_MEDIA_FOLDER = 'mageworx/optionfeatures/product/option/value';
    const RESIZED_FOLDER = 'resized';

    protected Filesystem $filesystem;
    protected StoreManagerInterface $storeManager;
    protected AdapterFactory $imageFactory;

    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        AdapterFactory $imageFactory
    ) {
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getBaseMediaPath($media_folder = self::MAGEWORX_OPTION_MEDIA_FOLDER)
    {
        $path = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath() . $media_folder;

        if (!file_exists($path) || !is_dir($path)) {
            $writer = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $writer->create($media_folder);
        }

        return $path;
    }

    protected function getResizedPath($file, $width, $height, $media_folder = '')
    {
        if (empty($media_folder)) {
            $media_folder = self::MAGEWORX_OPTION_MEDIA_FOLDER . '/' . self::RESIZED_FOLDER;
        }

        return $this->getBaseMediaPath($media_folder) . '/' . $height . '/' . $width . '/' . $file;
    }

    public function getResizedMediaUrl($image, $width, $height, $media_folder = '')
    {
        if (!$image) {
            return false;
        }

        $url = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $media_folder . '/' . self::RESIZED_FOLDER;

        $url .= '/' . $height . '/' . $width . '/' . $image;

        return $url;
    }

    public function resize($file, $width = null, $height = null)
    {
        $base_root = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)
            ->getAbsolutePath();
        $base_media = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath();
        $base_media = substr($base_media, strlen($base_root) - 1);
        $media_folder = self::MAGEWORX_OPTION_MEDIA_FOLDER;
        $imageResized = null;

        $absolutePath = $base_root . $base_media . $media_folder . $file;
        if (file_exists($absolutePath)) {
            $imageResized = $this->getResizedPath($file, $width, $height, $media_folder . '/' . self::RESIZED_FOLDER);
        } else {
            return false;
        }

        if ($imageResized && !file_exists($imageResized)) { // Only resize image if not already exists.
            //create image factory...
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            //save image
            $imageResize->save($imageResized);
        }

        return $this->getResizedMediaUrl($file, $width, $height, $media_folder);
    }
}
