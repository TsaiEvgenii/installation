<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\Service;


use Magento\Catalog\Model\ImageUploader;
use Psr\Log\LoggerInterface;

class PrepareImgToSave
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::PrepareImgToSaveService]: ';
    public function __construct(
        protected ImageUploader $imageUploader,
        protected LoggerInterface $logger
    ) {
    }

    public function prepare($imgData): string
    {
        $imgPath = '';

        try {
            if ($imgData[0]['tmp_name'] ?? false) {
                $imageName = $imgData[0]['name'] ?? '';
                if ($imageName !== '') {
                    $imgPath = $this->imageUploader->moveFileFromTmp($imageName, true);
                }
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $imgPath;
    }
}