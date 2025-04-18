<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\OrderUpgrader\Model\Service;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    /**
     * Config prefix for module
     */
    private const CONFIG_PREFIX = 'belvg_order_upgrader';


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * Get config value
     *
     * @param string $field
     * @param int|null $store
     * @param string $scope
     * @return mixed
     */
    public function getConfig(string $field, ?int $store = null, string $scope = 'general'): mixed
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PREFIX . '/' . $scope . '/' . $field,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        $storeId = $storeId ?? $this->getCurrentStoreId();
        return (bool)$this->getConfig('is_enabled', $storeId);
    }

    /**
     * Get upgrade options configuration
     *
     * @param int|null $storeId
     * @return array
     */
    public function getUpgradeOptions(?int $storeId = null): array
    {
        $storeId = $storeId ?? $this->getCurrentStoreId();
        $optionTypes = $this->getOptionTypes($storeId);
        $optionValues = $this->getOptionValues($storeId);

        $formattedOptions = [];

        foreach ($optionTypes as $optionType) {
            $code = $optionType['code'];
            $label = $optionType['label'];

            // Find values for this option type
            $values = [];
            foreach ($optionValues as $value) {
                if (($value['option_code'] ?? '') === $code) {
                    $values[] = [
                        'label' => $value['label'],
                        'value' => $value['value'],
                        'file' => (isset($value['file']) && $value['file'])
                            ? $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $value['file']
                            : ''
                    ];
                }
            }

            // Only add option if it has values
            if (!empty($values)) {
                $formattedOptions[$code] = [
                    'code' => $code,
                    'label' => $label,
                    'values' => $values
                ];
            }
        }

        return $formattedOptions;
    }

    /**
     * Get option types from configuration
     *
     * @param int $storeId
     * @return array
     */
    private function getOptionTypes(int $storeId): array
    {
        $types = $this->getConfig('types', $storeId, 'options_config');

        if (!$types) {
            return [];
        }

        try {
            if(!is_array($types)){
                $types = $this->serializer->unserialize($types);
            }
            return $types;
        } catch (\Exception $e) {
            // Return empty array on error
        }

        return [];
    }

    /**
     * Get option values from configuration
     *
     * @param int $storeId
     * @return array
     */
    private function getOptionValues(int $storeId): array
    {
        $values = $this->getConfig('values', $storeId, 'options_config');

        if (!$values) {
            return [];
        }

        try {
            if(!is_array($values)){
                $values = $this->serializer->unserialize($values);
            }
            return $values;
        } catch (\Exception $e) {
            // Return empty array on error
        }

        return [];
    }

    /**
     * Get material images configuration
     *
     * @param int|null $storeId
     * @return array
     */
    public function getMaterialImages(?int $storeId = null): array
    {
        $storeId = $storeId ?? $this->getCurrentStoreId();
        $materialImagesData = $this->getConfig('material_image', $storeId, 'options_config');

        if (!$materialImagesData) {
            return [];
        }

        try {
            if(!is_array($materialImagesData)){
                $materialImagesData = $this->serializer->unserialize($materialImagesData);
            }

            $result = [];
            foreach ($materialImagesData as $item) {
                if (isset($item['material_id']) && isset($item['file']) && $item['file']) {
                    $result[$item['material_id']] = [
                        'material_id' => $item['material_id'],
                        'image_url' => $this->storeManager->getStore()
                                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $item['file']
                    ];
                }
            }

            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get current store ID
     *
     * @return int
     */
    protected function getCurrentStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
