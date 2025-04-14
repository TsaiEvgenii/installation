<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Flag;
use BelVG\MageWorxOptionServerSideRender\Model\Service\UpdateRuleCacheStatusService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\SalesRule\Api\Data\RuleInterface;
use PHPUnit\Framework\TestCase;

class UpdateRuleCacheStatusServiceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var Flag|\PHPUnit\Framework\MockObject\MockObject
     */
    private $flagMock;

    private UpdateRuleCacheStatusService $model;

    /**
     * @dataProvider getData
     */
    public function testExecute($rule, $websiteId, $flagData,$expectedResult)
    {
       $selfLoadFlagMock  = $this->getMockBuilder(Flag::class)
                       ->disableOriginalConstructor()
                       ->getMock();
       $selfLoadFlagMock->method('getFlagData')
                        ->willReturn($flagData);
       $selfLoadFlagMock->expects(self::once())
                        ->method('setFlagData')
                        ->with($expectedResult);
       $selfLoadFlagMock->expects(self::once())
                        ->method('save');
       $this->flagMock->method('loadSelf')
                      ->willReturn($selfLoadFlagMock);
       $this->model->execute($rule, $websiteId);
    }

    public function getData()
    {
        return [
            'rule-not-exits'=>[
                'rule'=>11,
                'websiteId'=>1,
                'flagData'=>'',
                'result'=>[1=>11, 0=>'']
            ],
            'rule-exists-in-other-website'=>[
                'rule'=>11,
                'websiteId'=>1,
                'flagData'=>[2=>13],
                'result'=>[1=>11, 2=>13]
            ],
            'another-rule-exists'=>[
                'rule'=>11,
                'websiteId'=>1,
                'flagData'=>[1=>13],
                'result'=>[1=>11]
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->flagMock  = $this->getMockBuilder(Flag::class)
                        ->disableOriginalConstructor()
                        ->getMock();   
        $this->model = new UpdateRuleCacheStatusService($this->flagMock);
    }
}