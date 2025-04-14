<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Command;

use BelVG\MageWorxOptionServerSideRender\Model\Service\CacheCleanService;
use BelVG\MageWorxOptionServerSideRender\Model\Service\CheckCacheStatusService;
use BelVG\MageWorxOptionServerSideRender\Model\Service\UpdateRuleCacheStatusService;
use BelVG\SaleCountdown\Api\Locator\GetActualRuleInterface;
use Magento\Framework\DataObject;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class DetectChangingSaleRules
{
    private GetActualRuleInterface $getActualRule;
    private StoreManagerInterface $storeManager;
    private CheckCacheStatusService $cacheStatusService;
    private CacheCleanService $cacheCleanService;
    private UpdateRuleCacheStatusService $updateRuleCacheStatusService;

    /**
     * DetectChangingSaleRules constructor.
     * @param GetActualRuleInterface $getActualRule
     * @param StoreManagerInterface $storeManager
     * @param RuleRepositoryInterface $ruleRepository
     * @param CheckCacheStatusService $cacheStatusService
     * @param CacheCleanService $cacheCleanService
     * @param UpdateRuleCacheStatusService $updateRuleCacheStatusService
     */
    public function __construct(
        GetActualRuleInterface $getActualRule,
        StoreManagerInterface $storeManager,
        CheckCacheStatusService $cacheStatusService,
        CacheCleanService $cacheCleanService,
        UpdateRuleCacheStatusService $updateRuleCacheStatusService
    ) {

        $this->getActualRule = $getActualRule;
        $this->storeManager = $storeManager;
        $this->cacheStatusService = $cacheStatusService;
        $this->cacheCleanService = $cacheCleanService;
        $this->updateRuleCacheStatusService = $updateRuleCacheStatusService;
    }
    public function execute()
    {
        foreach ($this->storeManager->getStores(true) as $store) {
            $countDown = $this->getActualRule->getActualRule(null, (int)$store->getWebsiteId())?? new DataObject();
            if ($this->checkCacheStatus((int)$countDown->getRuleId(), (int)$store->getWebsiteId())) {
                $this->cacheCleanService->execute();
                $this->updateRuleCacheStatusService->execute((int)$countDown->getRuleId(), (int)$store->getWebsiteId());
            }
        }
    }

    private function checkCacheStatus($ruleId, int $websiteId)
    {
        return $this->cacheStatusService->execute($ruleId, $websiteId);
    }
}
