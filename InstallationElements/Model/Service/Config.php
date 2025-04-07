<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /** @var string */
    const CONFIG_PREFIX = 'belvg_installation_elements';

    /** @var string[] */
    const STANDARD_PRICE_FIELDS
        = [
            'window_sku_prefix',
            'door_sku_prefix',
            'window_sqr',
            'door_sqr',
            'door_type',
            'layer_glass'
        ];
    /** @var string[] */
    const OPTIONAL_PRICE_FIELDS
        = [
            'construction',
            'internal',
        ];

    const SUPPLEMENT_PRICE_FIELDS
        = [
            'driving',
            'high_ground_floor',
            'assembly_1_floor',
            'above_1_floor'
        ];

    const ROUTEPLANNER_FIELDS
        = [
            'type_id',
            'status_after_creation',
            'request_email_tpl',
            'create_ticket_status'
        ];

    const CONDITIONS_FILE_UPLOAD_DIR = 'ConditionsForInstallation';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private StoreManagerInterface $storeManager,
        protected DirectoryList $directoryList,
        private SerializerInterface $serializer
    ) {
    }

    public function getConfig(string $field, int $store = null, string $scope = 'general')
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PREFIX . '/' . $scope . '/' . $field,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function isEnabled($storeId = null)
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        return $this->getConfig('is_enabled', $storeId) ?? null;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getConditionsFile($storeId = null): string
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        if ($conditionsFile = $this->getConditionFileName()) {
            $url = $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::CONDITIONS_FILE_UPLOAD_DIR;
            $url .= '/' . $conditionsFile;

            return $url;
        }

        return '';
    }
    public function getConditionFilePath($storeId = null){
        $storeId = $storeId ?: $this->getCurrentStoreId();
        if ($conditionsFileName = $this->getConditionFileName($storeId)) {
            $mediaPath = $this->directoryList->getPath('media');
            $filePath = $mediaPath . '/' . self::CONDITIONS_FILE_UPLOAD_DIR . '/' . $conditionsFileName;

            if (file_exists($filePath)) {
                return  $filePath;
            }
        }

        return '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getConditionFileName($storeId = null)
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();

        return $this->getConfig('file', $storeId, 'conditions') ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getSubscribersList($storeId = null):string
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        return (string)$this->getConfig('subscribers_list', $storeId) ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getStandardPriceData($storeId = null): array
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        $data = [];
        foreach (self::STANDARD_PRICE_FIELDS as $fieldName) {
            $value = $this->getConfig($fieldName, $storeId, 'standard_price');
            if (is_string($value) && $this->isJson($value)) {
                $value = $this->serializer->unserialize($value);
            }
            $data[$fieldName] = $value;
        }

        return $data;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getOptionalPriceData($storeId = null): array
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        $data = [];
        foreach (self::OPTIONAL_PRICE_FIELDS as $fieldName) {
            $value = $this->getConfig($fieldName, $storeId, 'optional_price');
            if (is_string($value) && $this->isJson($value)) {
                $value = $this->serializer->unserialize($value);
            }
            $data[$fieldName] = $value;
        }

        return $data;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getSupplementPriceData($storeId = null): array
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        $data = [];
        foreach (self::SUPPLEMENT_PRICE_FIELDS as $fieldName) {
            $value = $this->getConfig($fieldName, $storeId, 'supplement_price');
            if (is_string($value) && $this->isJson($value)) {
                $value = $this->serializer->unserialize($value);
            }
            $data[$fieldName] = $value;
        }

        return $data;
    }
    public function getRouteplannerSettings($storeId = null): array
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        $data = [];
        foreach (self::ROUTEPLANNER_FIELDS as $fieldName) {
            $value = $this->getConfig($fieldName, $storeId, 'routeplanner');
            if (is_string($value) && $this->isJson($value)) {
                $value = $this->serializer->unserialize($value);
            }
            $data[$fieldName] = $value;
        }

        return $data;
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function getCurrentStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    private function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function prepareSettingsData($data): array
    {
        $preparedData = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $preparedData[$key] = explode(',', $value);
            }
            if (is_array($value)) {
                foreach ($value as $item) {
                    $itemData = [];
                    foreach ($item as $itemKey => $itemValue) {
                        $itemKey = str_replace($key . '_', '', $itemKey);
                        if ($itemKey === 'sku_prefix') {
                            $itemData[$itemKey] = explode(',', $itemValue);
                        } else {
                            $itemData[$itemKey] = $itemValue;

                        }
                    }
                    $preparedData[$key][] = $itemData;
                }
            }
        }

        return $preparedData;
    }
    public function getBelVGHelpdeskType() :string {
        return 'installation';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getReminderTemplate($storeId = null){
        $storeId = $storeId ?: $this->getCurrentStoreId();
        return $this->getConfig('template', $storeId, 'reminder') ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getReminderStatus($storeId = null){
        $storeId = $storeId ?: $this->getCurrentStoreId();
        return $this->getConfig('status', $storeId, 'reminder') ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getReminderDelay($storeId = null){
        $storeId = $storeId ?: $this->getCurrentStoreId();
        return $this->getConfig('delay', $storeId, 'reminder') ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getReminderSubscribers($storeId = null){
        $storeId = $storeId ?: $this->getCurrentStoreId();
        return $this->getConfig('subscribers_list', $storeId, 'reminder') ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getScaffoldingPriceData($storeId = null): array
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        $data = [
            'high_ground_floor_start_price' => $this->getConfig('high_ground_floor_start_price', $storeId, 'scaffolding_price'),
            'per_element_price' => $this->getConfig('per_element_price', $storeId, 'scaffolding_price')
        ];

        return $data;
    }
}