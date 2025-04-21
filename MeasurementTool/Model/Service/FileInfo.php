<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\Service;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class FileInfo
{

    /** @var  */
    protected $mediaDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     */
    public function __construct(
        protected Filesystem $filesystem,
        protected Mime $mime
    ) {

    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @param string $mediaPath
     *
     * @return string
     */
    public function getMimeType($fileName, $mediaPath = '')
    {
        $filePath = $mediaPath . '/' . ltrim($fileName, '/');
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);

        $result = $this->mime->getMimeType($absoluteFilePath);
        return $result;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @param string $mediaPath
     *
     * @return array
     */
    public function getStat($fileName, $mediaPath = '')
    {
        $filePath = $mediaPath . '/' . ltrim($fileName, '/');

        $result = $this->getMediaDirectory()->stat($filePath);

        return $result;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @param string $mediaPath
     *
     * @return bool
     */
    public function isExist($fileName, $mediaPath = '')
    {
        $filePath = $mediaPath . '/' . ltrim($fileName, '/');

        return $this->getMediaDirectory()->isExist($filePath);
    }
}