<?php
/**
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);

namespace BelVG\OrderUpgrader\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Exception\FileSystemException;

class FileUploadBackend extends ArraySerialized
{
    /**
     * Upload path relative to media directory
     */
    private const UPLOAD_DIR = 'belvg/orderupgrader/images';

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        private readonly UploaderFactory $uploaderFactory,
        private readonly Filesystem $filesystem,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Process data before saving
     *
     * @return $this
     * @throws FileSystemException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $fieldId = $this->getPath();
        $configFieldName = pathinfo($fieldId, PATHINFO_BASENAME);

        if (is_array($value)) {
            $result = [];

            foreach ($value as $rowId => $row) {
                if ($rowId === '__empty') {
                    continue;
                }

                $fileField = $_FILES['groups']['name']['options_config']['fields'][$configFieldName]['value'][$rowId]['file'] ?? null;

                if ($fileField) {
                    try {
                        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                        $uploader = $this->uploaderFactory->create([
                            'fileId' => 'groups[options_config][fields][' . $configFieldName . '][value][' . $rowId
                                . '][file]'
                        ]);

                        $uploader->setAllowedExtensions([
                            'jpg',
                            'jpeg',
                            'png',
                            'gif'
                        ]);
                        $uploader->setAllowRenameFiles(true);

                        $uploadResult = $uploader->save($mediaDirectory->getAbsolutePath(self::UPLOAD_DIR));
                        if (isset($uploadResult['file'])) {
                            $row['file'] = self::UPLOAD_DIR . '/' . ltrim($uploadResult['file'], '/');
                        }
                    } catch (\Exception $e) {
                        $row['file'] = $row['file'] ?? '';
                    }
                } elseif (isset($row['file']) && is_array($row['file']) && isset($row['file'][0])) {
                    $row['file'] = $row['file'][0];
                } elseif (!isset($row['file']) || is_array($row['file'])) {
                    $row['file'] = '';
                }

                $result[$rowId] = $row;
            }

            $this->setValue($result);
        }

        return parent::beforeSave();
    }
}
