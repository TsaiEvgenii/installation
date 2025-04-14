<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Model\Service;

use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Psr\Log\LoggerInterface;

class TaxRateByStoreService
{
    const LOG_PREFIX = '[BelVG_LayoutCustomizer::TaxRateByStoreService]: ';

    protected $taxRateRepository;
    protected $scopeConfig;
    protected $serializer;
    protected $logger;

    public function __construct(
        TaxRateRepositoryInterface $taxRateRepository,
        ScopeConfigInterface $scopeConfig,
        Serializer $serializer,
        LoggerInterface $logger
    ) {
        $this->taxRateRepository = $taxRateRepository;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * https://app.asana.com/0/1177395662263354/1177969533611915/f [All prices should be without Tax]
     *
     * @param $storeId
     * @return float
     */
    public function getTaxRateMultiplier($storeId) {
        $taxRateValue = 0;
        $rates = $this->scopeConfig->getValue(
            'layout_customizer/general/mapping_store_tax',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        try {
            $rates = $this->serializer->unserialize($rates);

            foreach ($rates as $rate) {
                if ($rate['store'] == $storeId) {
                    $taxRateId = $rate['tax_rate'];

                    /** @var \Magento\Tax\Api\Data\TaxRateInterface $taxRate */
                    $taxRate = $this->taxRateRepository->get($taxRateId);
                    $taxRateValue = $taxRate->getRate();

                    break;
                }
            }
            unset($rate);
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . 'something went wrong: "%s"',
                $e->getMessage()
            ));
        }

        if ($taxRateValue) {
            return (float)($taxRateValue / 100) + 1;
        }

        return 1;
    }
}
