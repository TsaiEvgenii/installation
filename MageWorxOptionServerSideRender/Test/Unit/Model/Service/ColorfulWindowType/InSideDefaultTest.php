<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service\ColorfulWindowType;

use BelVG\MageWorxOptionServerSideRender\Model\Dto\ColorDescription;
use BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType\AbstractColorProcessor;
use BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType\InSideDefault;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class InSideDefaultTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var AbstractColorProcessor|\PHPUnit\Framework\MockObject\MockObject
     */
    private $next;

    private $model;

    /**
     * @param $inColorDescription
     * @param $outColorDescription
     * @param $expectedResult
     * @dataProvider getData
     */
    public function testGetType($inColorDescription, $outColorDescription, $expectedResult)
    {
        $result = $this->model->getType($inColorDescription, $outColorDescription);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @param $inColorDescription
     * @param $outColorDescription
     * @dataProvider getNextData
     */
    public function testGetNextType($inColorDescription, $outColorDescription)
    {
        $this->next->expects(self::once())
            ->method('getType')
            ->with($inColorDescription, $outColorDescription)
            ->willReturn('another_processor');
        $result = $this->model->getType($inColorDescription, $outColorDescription);
        $this->assertSame('another_processor', $result);
    }
    public function getData()
    {
        return [
            'inSideIsDefault'=>[
                'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>true]),
                'outColor'=> new ColorDescription(['type'=>'outside', 'is_default'=>false]),
                'result'=>'out_other_white'
            ]
        ];
    }

    public function getNextData()
    {
        return [
            'two-default'=>[
                'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>true]),
                'outColor'=> new ColorDescription(['type'=>'outside', 'is_default'=>true]),
            ],
            'no-default'=>[
                'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>false]),
                'outColor'=> new ColorDescription(['type'=>'outside', 'is_default'=>false]),
            ],
            'default-outcolor'=>[
                'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>false]),
                'outColor'=> new ColorDescription(['type'=>'outside', 'is_default'=>true]),
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->next  = $this->getMockBuilder(AbstractColorProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectFactory  = $this->getMockBuilder(ObjectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = new InSideDefault($this->next, $objectFactory);
    }
}
