<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\Service;


class PrepareEntityImgToSave extends PrepareImgToSave
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::PrepareEntityImgToSaveService]: ';
    public function prepare($imgData): string
    {
        $imgPath = '';

        try {
            if ($imgData['tmp_name'] ?? false) {
                $imageName = $imgData['name'] ?? '';
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