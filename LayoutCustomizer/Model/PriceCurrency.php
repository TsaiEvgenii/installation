<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\PriceCurrency as DefaultPriceCurrency;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface as Logger;

class PriceCurrency
{
    const LOG_PREFIX = '[BelVG_LayoutCustomizer::PriceCurrency]: ';
    const XML_PATH_LAYOUT_CURRENCY_BASE = 'layout_customizer/general/base_currency';

    private $storeManager;
    private $defaultPriceCurrency;
    private $currencyFactory;
    private $config;
    private $logger;
    private $layoutCurrency = null;

    public function __construct(
        StoreManagerInterface $storeManager,
        DefaultPriceCurrency $defaultPriceCurrency,
        CurrencyFactory $currencyFactory,
        ReinitableConfigInterface $config,
        Logger $logger
    ) {
        $this->storeManager = $storeManager;
        $this->defaultPriceCurrency = $defaultPriceCurrency;
        $this->currencyFactory = $currencyFactory;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param $amount
     * @param null $scope
     * @param null $currency
     * @return float
     */
    public function convert($amount, $scope = null, $currency = null)
    {
        $currentCurrency = $this->defaultPriceCurrency->getCurrency($scope, $currency);

        try {
            return $this->getLayoutBaseCurrency()
                ->convert($amount, $currentCurrency);
        } catch (\Exception $e) {
            $this->logger->critical(sprintf(
                self::LOG_PREFIX . 'something went wrong: "%s"',
                $e->getMessage()
            ));

            return $amount;
        }

    }

    /**
     * @return \Magento\Directory\Model\Currency
     * @throws NoSuchEntityException
     */
    protected function getLayoutBaseCurrency()
    {
        if (null === $this->layoutCurrency) {
            $layoutBaseCurrencyCode = $this->getConfig(self::XML_PATH_LAYOUT_CURRENCY_BASE);
            $this->layoutCurrency = $this->currencyFactory->create()->load($layoutBaseCurrencyCode);
        }

        return $this->layoutCurrency;
    }

    /**
     * Return config value
     *
     * @param $path
     * @return mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig($path)
    {
        $data = $this->config->getValue($path, ScopeInterface::SCOPE_STORE, $this->getStore()->getCode());
        if ($data === null) {
            $data = $this->config->getValue($path);
        }

        return $data === false ? null : $data;
    }

    /**
     * Get store model
     *
     * @param null $scope
     * @return \Magento\Store\Api\Data\StoreInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStore($scope = null)
    {
        try {
            if (!$scope instanceof Store) {
                $scope = $this->storeManager->getStore($scope);
            }
        } catch (\Exception $e) {
            $this->logger->critical(sprintf(
                self::LOG_PREFIX . 'something went wrong: "%s"',
                $e->getMessage()
            ));
            $scope = $this->storeManager->getStore();
        }

        return $scope;
    }
}
