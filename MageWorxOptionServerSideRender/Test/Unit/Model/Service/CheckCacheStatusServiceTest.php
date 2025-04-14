<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Flag;
use BelVG\MageWorxOptionServerSideRender\Model\Service\CheckCacheStatusService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\SalesRule\Api\Data\RuleInterface;
use PHPUnit\Framework\TestCase;

class CheckCacheStatusServiceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    private $model;
    /**
     * @var Flag|\PHPUnit\Framework\MockObject\MockObject
     */
    private $flag;

    /**
     * @param $rule
     * @param $websiteId
     * @dataProvider getData
     */
    public function testExecute($rule, $websiteId, $flagData, $expectedResult)
    {
        $this->flag->expects(self::once())
                  ->method('loadSelf')
                  ->willReturnSelf();
        $this->flag->method('getFlagData')
                  ->willReturn($flagData);
        $result = $this->model->execute($rule, $websiteId);
        $this->assertSame($expectedResult, $result);
    }

    public function getData()
    {
        return [
            'check-cache-valid'=>[
                'rule'=>12,
                'website'=>4,
                'flagData'=>[4=>12],
                'expectedResult'=>false
            ],
            'check-cache-valid-string-id'=>[
                'rule'=>12,
                'website'=>4,
                'flagData'=>[4=>12],
                'expectedResult'=>false
            ],
            'check-cache-invalid'=>[
                'rule'=>12,
                'website'=>4,
                'flagData'=>null,
                'expectedResult'=>true
            ],
            'check-cache-invalid-empty-array-return'=>[
                'rule'=>12,
                'website'=>4,
                'flagData'=>[],
                'expectedResult'=>true
            ],
            'check-cache-invalid--array-return-another-store'=>[
                'rule'=>12,
                'website'=>4,
                'flagData'=>[3=>11],
                'expectedResult'=>true
            ],
            'check-cache-invalid--array-return-string'=>[
                'rule'=>12,
                'website'=>4,
                'flagData'=>'',
                'expectedResult'=>true
            ],
            'check-cache-invalid--array-return-website-with-another-rule'=>[
                'rule'=>12,
                'website'=>4,
                'flagData'=>[4=>11],
                'expectedResult'=>true
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->flag  = $this->getMockBuilder(Flag::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new CheckCacheStatusService($this->flag);
    }

    private function createRule($data)
    {
        $rule  = $this->getMockBuilder(RuleInterface::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        foreach ($data as $method => $value) {
            $rule->method($method)
                   ->willReturn($value);
        }
        return $rule;
    }
}
