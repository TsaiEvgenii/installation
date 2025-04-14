<?php


namespace BelVG\LayoutCustomizer\Model\Config;


class FileProcessor
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    /**
     * @param  string $fileId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($fileId)
    {
        try {
            $result = $this->saveFile($fileId, $this->getAbsoluteMediaPath());
            $result['name'] = $result['file'];
            $result['url'] = $this->getMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $result;
    }

    /**
     * @param string $file
     * @return string
     */
    private function getMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . \BelVG\LayoutCustomizer\Model\Config::MEDIA_FOLDER . '/' . $file;
    }

    /**
     * Retrieve absolute temp media path
     *
     * @return string
     */
    private function getAbsoluteMediaPath()
    {
        return $this->mediaDirectory->getAbsolutePath(\BelVG\LayoutCustomizer\Model\Config::MEDIA_FOLDER);
    }

    /**
     * @param string $fileId
     * @param string $destination
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveFile($fileId, $destination)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);

        return $uploader->save($destination);
    }
}
