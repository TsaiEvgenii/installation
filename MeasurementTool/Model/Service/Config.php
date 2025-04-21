<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\Service;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    private const CONFIG_PREFIX = 'belvg_measurement_tool';
    private const CONFIG_PATH_IS_ENABLED = 'is_enabled';
    private const CONFIG_PATH_ELEMENT_TYPE = 'element_type';
    private const CONFIG_PATH_WIDGET_LABEL = 'widget_label';
    private const CONFIG_PATH_WIDGET_IMAGE = 'widget_image';
    private const CONFIG_PATH_WIDGET_IMAGE_UPLOAD_DIR = 'measurement_tool/widget/image';


    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private DirectoryList $directoryList,
        private \Magento\Framework\UrlInterface $urlBuilder,
        private SerializerInterface $serializer,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @param string $field
     * @param int|null $website
     * @param string $scope
     * @return mixed
     */
    public function getConfig(string $field, int $store = null, string $scope = 'settings'): mixed
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PREFIX . '/' . $scope . '/' . $field,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnabled(?int $storeId = null) :bool {
        return (bool)$this->getConfig(self::CONFIG_PATH_IS_ENABLED, $storeId);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getElementTypes(?int $storeId = null){
        $storeId = $storeId ?: $this->getCurrentStoreId();
        $value = $this->getConfig(self::CONFIG_PATH_ELEMENT_TYPE, $storeId);
        if (is_string($value) && $this->isJson($value)) {
            $value = $this->serializer->unserialize($value);
        }

        return $value;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getWidgetLabel($storeId = null): string
    {
        $storeId = $storeId ?: $this->getCurrentStoreId();
        return $this->getConfig(self::CONFIG_PATH_WIDGET_LABEL, $storeId) ?? '';
    }

    /**
     * @throws NoSuchEntityException
     * @throws FileSystemException
     */
    public function getWidgetImagePath($storeId = null): string
    {
        $imagePath = '';
        $storeId = $storeId ?: $this->getCurrentStoreId();

        $imageName = $this->getConfig(self::CONFIG_PATH_WIDGET_IMAGE, $storeId);
        if($imageName){
            $imagePath = $this->storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
                self::CONFIG_PATH_WIDGET_IMAGE_UPLOAD_DIR
                . '/'
                . $imageName;
        }

        return $imagePath;
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

}