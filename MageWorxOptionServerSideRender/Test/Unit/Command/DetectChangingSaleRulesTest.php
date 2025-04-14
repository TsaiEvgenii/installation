<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Command;

use BelVG\MageWorxOptionServerSideRender\Command\DetectChangingSaleRules;
use BelVG\MageWorxOptionServerSideRender\Model\Service\CacheCleanService;
use BelVG\MageWorxOptionServerSideRender\Model\Service\CheckCacheStatusService;
use BelVG\MageWorxOptionServerSideRender\Model\Service\UpdateRuleCacheStatusService;
use BelVG\SaleCountdown\Api\Data\CountdownInterface;
use BelVG\SaleCountdown\Api\Locator\GetActualRuleInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;

class DetectChangingSaleRulesTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManager;
    /**
     * @var GetActualRuleInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $actualRuleServiceMock;

    private DetectChangingSaleRules $model;
    private \PHPUnit\Framework\MockObject\MockObject $salesRuleRepository;
    private \PHPUnit\Framework\MockObject\MockObject $checkCacheStatusServiceMock;
    /**
     * @var CacheCleanService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheCleanMock;
    /**
     * @var UpdateRuleCacheStatusService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $updateRuleCacheState;
    /**
     * @var \Magento\SalesRule\Model\Data\Rule|\PHPUnit\Framework\MockObject\MockObject
     */
    private $rule;

    /**
     * @dataProvider getData
     */
    public function testExecute($storesData,
                                $params,
                                $salesCountDownsData,
                                $checkServiceWith,
                                $checkStatusReturn,
                                $cacheCleanStatus,
                                $updateCacheStatus)
    {
        $salesCountDatas = $this->getSalesCountDownData($salesCountDownsData);
        $stores = $this->getStores($storesData);
        $this->storeManager->method('getStores')
                           ->with(true)
                           ->willReturn($stores);
        $this->actualRuleServiceMock->expects(self::atLeast(count($stores)))
                                    ->method('getActualRule')
                                    ->withConsecutive(...$params)
                                    ->willReturnOnConsecutiveCalls(...$salesCountDatas);
        $this->checkCacheStatusServiceMock->expects(self::exactly(count($checkServiceWith)))
                                          ->method('execute')
                                          ->withConsecutive(...$checkServiceWith)
                                          ->willReturn(...$checkStatusReturn);
        $this->cacheCleanMock->expects(self::exactly(count($cacheCleanStatus)))
                             ->method('execute');
        $this->updateRuleCacheState->expects(self::exactly(count($updateCacheStatus)))
                                   ->method('execute')
                                   ->withConsecutive(...$updateCacheStatus);

        $this->model->execute();
    }

    public function getData()
    {
        return [
            'multi-store'=>[
                'stores'=>['2','3','4'],
                'actualRule'=>[[null,2],[null,3],[null,4]],
                'salesCountDown'=>[null, null, 2],
                'checkCacheStatus'=>[[0,2], [0,3],[2, 4]],
                'checkCacheStatusReturn'=>[false, false, true],
                'cacheCleanStatus'=>[4],
                'updateCacheStatus' => [[2, 4]]
            ],
            'multi-store-cache-clean-not-needed'=>[
                'stores'=>['2','3','4'],
                'actualRule'=>[[null,2],[null,3],[null,4]],
                'salesCountDown'=>[null, null, 2],
                'checkCacheStatus'=>[[0,2], [0,3],[2, 4]],
                'checkCacheStatusReturn'=>[false,false,false],
                'cacheCleanStatus'=>[],
                'updateCacheStatus' => []
            ],
            'multi-store-no-actual-rule'=>[
                'stores'=>['2','3','4'],
                'actualRule'=>[[null,2],[null,3],[null,4]],
                'salesCountDown'=>[null, null, null],
                'checkCacheStatus'=>[[0,2], [0,3],[0, 4]],
                'checkCacheStatusReturn'=>[false, false, false],
                'cacheCleanStatus'=>[],
                'updateCacheStatus' => []
            ],
            'multi-store-many-rules'=>[
                'stores'=>['2','3','4'],
                'actualRule'=>[[null,2],[null,3],[null,4]],
                'salesCountDown'=>[2,2,2],
                'checkCacheStatus'=>[[2, 2], [2, 3], [2, 4]],
                'checkCacheStatusReturn'=>[true, true, true],
                'cacheCleanStatus'=>[2,3,4],
                'updateCacheStatus' => [[2, 2], [2, 3], [2, 4]]

         ]
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->storeManager  = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->actualRuleServiceMock  = $this->getMockBuilder(GetActualRuleInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->checkCacheStatusServiceMock  = $this->getMockBuilder(CheckCacheStatusService::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->cacheCleanMock  = $this->getMockBuilder(CacheCleanService::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->updateRuleCacheState  = $this->getMockBuilder(UpdateRuleCacheStatusService::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new DetectChangingSaleRules($this->actualRuleServiceMock,
                                                   $this->storeManager,
                                                   $this->checkCacheStatusServiceMock,
                                                   $this->cacheCleanMock,
                                                   $this->updateRuleCacheState
                                                 );
    }

    private function getSalesCountDownData($salesCountDownsData)
    {
        $salesCountData =[];
        foreach ($salesCountDownsData as $salesCountDownData){
            if($salesCountDownData !== null){
                $countDown  = $this->getMockBuilder(CountdownInterface::class)
                    ->disableOriginalConstructor()
                    ->getMock();
                $countDown->method('getRuleId')
                    ->willReturn($salesCountDownData);
                $salesCountData[] = $countDown;
            }else{
                $salesCountData[] = null;
            }
        }
        return $salesCountData;
    }

    private function getStores($storesData)
    {
        $stores = [];
        foreach ($storesData as $websiteId){
            $store = $this->getMockBuilder(StoreInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
            $store->method('getWebsiteId')
                ->willReturn($websiteId);
            $stores[] = $store;
        }
        return $stores;
    }
}