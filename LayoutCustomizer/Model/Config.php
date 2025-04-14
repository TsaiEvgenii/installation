<?php


namespace BelVG\LayoutCustomizer\Model;


class Config
{
    const MEDIA_FOLDER = 'belvg_layoutcustomizer';
    const ROUTE_ADDR = 'belvg_layoutcustomizer';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getBaseMediaPath()
    {
        $path = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath() . self::MEDIA_FOLDER;

        if (!file_exists($path) || !is_dir($path)) {
            $writer = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $writer->create(self::MEDIA_FOLDER);
        }

        return $path;
    }

    /**
     * @param $image
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl($image)
    {
        if (!$image) {
            return false;
        }

        $url = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::MEDIA_FOLDER;

        $url .= '/' . $image;

        return $url;
    }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaPath($file)
    {
        return $this->getBaseMediaPath() . '/' . $file;
    }
}