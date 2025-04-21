<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\Service;


use BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;
use BelVG\MeasurementTool\Api\MeasurementToolImageRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

class SaveMeasurementToolImg
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::SaveMeasurementToolImgService]: ';

    public function __construct(
        protected \BelVG\MeasurementTool\Model\Service\PrepareEntityImgToSave $prepareEntityImgToSave,
        protected \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterfaceFactory $measurementToolImageFactory,
        protected MeasurementToolImageRepositoryInterface $measurementToolImageRepository,
        protected FilterBuilder $filterBuilder,
        protected FilterGroupBuilder $filterGroupBuilder,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected LoggerInterface $logger
    ) {
    }

    public function save($images, MeasurementToolInterface $measurementTool, $oldMeasurementToolImages): void
    {
        try {
            if (count($images) === 0) {
                foreach ($oldMeasurementToolImages as $existingImage) {
                    $this->measurementToolImageRepository->delete($existingImage);
                }
                return;
            }
            foreach ($images as $image) {
                if ($imagePath = $this->prepareEntityImgToSave->prepare($image)) {
                    /** @var MeasurementToolImageInterface $measurementToolImage */
                    $measurementToolImage = $this->measurementToolImageFactory->create();
                    $measurementToolImage->setMeasurementToolId($measurementTool->getEntityId());
                    $measurementToolImage->setImg($imagePath);
                    $this->measurementToolImageRepository->save($measurementToolImage);
                }
            }
            /** @var \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface $existingImage */
            foreach ($oldMeasurementToolImages as $existingImage) {
                $deleteFlag = true;
                $imgPath = $existingImage->getImg();
                foreach ($images as $image) {
                    if ($image['tmp_name'] ?? false) {
                        continue;
                    }
                    if (str_contains($image['url'], $imgPath)) {
                        $deleteFlag = false;
                        break;
                    }
                }
                if ($deleteFlag) {
                    $this->measurementToolImageRepository->delete($existingImage);
                }
            }

        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
    }
}