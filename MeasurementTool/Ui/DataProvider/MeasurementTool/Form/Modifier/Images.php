<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Ui\DataProvider\MeasurementTool\Form\Modifier;


use BelVG\MeasurementTool\Model\Service\FileInfo;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class Images implements ModifierInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::ImagesFormModifier]: ';

    public function __construct(
        protected FileInfo $fileInfo,
        protected StoreManagerInterface $storeManager,
        protected RequestInterface $request,
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
            $rooms = $data[$measurementToolId]['rooms'] ?? [];
            foreach ($rooms as $roomId => $room) {
                $elements = $room['elements'] ?? [];
                foreach ($elements as $elementId => $element) {
                    if ($imgPath = $element['img'] ?? false) {
                        if ($this->fileInfo->isExist($imgPath)) {
                            $stat = $this->fileInfo->getStat($imgPath);
                            $mime = $this->fileInfo->getMimeType($imgPath);
                            $url = $this->storeManager->getStore()->getBaseUrl(
                                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . $imgPath;
                            $fileParts = pathInfo($url);
                            $data[$measurementToolId]['rooms'][$roomId]['elements'][$elementId]['img'] = [];
                            $data[$measurementToolId]['rooms'][$roomId]['elements'][$elementId]['img'][] = [
                                'name'        => $fileParts['basename'] ?? '',
                                'url'         => $url,
                                'size'        => $stat['size'] ?? 0,
                                'type'        => $mime,
                                'previewType' => 'image'
                            ];
                        } else {
                            $data[$measurementToolId]['rooms'][$roomId]['elements'][$elementId]['img'] = '';
                        }
                    }
                }

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

    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}