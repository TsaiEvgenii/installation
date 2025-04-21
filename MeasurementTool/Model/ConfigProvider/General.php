<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\ConfigProvider;


use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class General implements \BelVG\MeasurementTool\Model\ConfigProviderInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::GeneralConfigProvider]: ';

    public function __construct(
        private \BelVG\MeasurementTool\Model\Service\Config $config,
        private \Magento\Store\Model\StoreManagerInterface $storeManager,
        private LoggerInterface $logger
    ) {
    }

    public function getConfig(): array
    {
        $config = [
            'is_enabled' => $this->config->isEnabled(),
        ];
        try {
            $elementTypeUrlKeys = [];
            $elementTypesData = $this->config->getElementTypes();
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
            foreach ($elementTypesData as $elementTypesDatum) {
                $elementTypeUrlKeys[$elementTypesDatum['element_type_code']] = $baseUrl
                    . $elementTypesDatum['element_type_url_key'];
            }
            $config['links'] = $elementTypeUrlKeys;
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $config;
    }
}