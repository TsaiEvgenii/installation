<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Ui\DataProvider\MeasurementTool\Form\Modifier;


use BelVG\MeasurementTool\Api\MeasurementToolRepositoryInterface;
use BelVG\MeasurementTool\Model\Service\FileInfo;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Psr\Log\LoggerInterface;

class MeasurementToolData implements ModifierInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::ConfigViewModel]: ';

    public function __construct(
        protected FileInfo $fileInfo,
        protected StoreManagerInterface $storeManager,
        protected RequestInterface $request,
        protected MeasurementToolRepositoryInterface $measurementToolRepository,
        protected LoggerInterface $logger
    ) {
    }

    public function modifyData(array $data): array
    {
        try {
            $measurementToolId = (int)$this->request->getParam('measurement_tool_id');
            if (!$measurementToolId) {
                return $data;
            }
            $data[$measurementToolId]['entity_id'] = $measurementToolId;
            $measurementToolDataModel = $this->measurementToolRepository->getById($measurementToolId);
            $data[$measurementToolId]['name'] = $measurementToolDataModel->getName() ?? '';
            $data[$measurementToolId]['description'] = $measurementToolDataModel->getDescription();
            $images = $measurementToolDataModel->getImages();
            if (count($images) === 0) {
                $data[$measurementToolId]['images'] = '';
            } else {
                $data[$measurementToolId]['images'] = [];
                foreach ($images as $image) {
                    $imgPath = $image->getImg();
                    if ($this->fileInfo->isExist($imgPath)) {
                        $stat = $this->fileInfo->getStat($imgPath);
                        $mime = $this->fileInfo->getMimeType($imgPath);
                        $url = !empty($imgPath) ?
                            $this->storeManager->getStore()->getBaseUrl(
                                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                            ) . $imgPath : '';
                        $fileParts = pathInfo($url);
                        $data[$measurementToolId]['images'][] = [
                            'name'        => $fileParts['basename'] ?? '',
                            'url'         => $url,
                            'size'        => $stat['size'] ?? 0,
                            'type'        => $mime,
                            'previewType' => 'image'
                        ];
                    }

                }
            }
            $rooms = $measurementToolDataModel->getRooms();
            foreach ($rooms as $room) {
                $roomData = [
                    'entity_id' => $room->getEntityId(),
                    'record_id' => $room->getRecordId(),
                    'name'      => $room->getName()
                ];
                $elements = $room->getElements();
                foreach ($elements as $element) {
                    $element->getData();
                    $roomData['elements'][] = $element->getData();
                }
                $data[$measurementToolId]['rooms'][] = $roomData;
            }

        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}