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
use BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType\DefaultColorProcessor;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class DefaultColorProcessorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var AbstractColorProcessor|\PHPUnit\Framework\MockObject\MockObject
     */
    private $next;
    private DefaultColorProcessor $model;

    /**
     * @dataProvider getData
     */
    public function testGetType($inColorDescription, $outColorDescription, $expectedResult)
    {
        $result = $this->model->getType($inColorDescription, $outColorDescription);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getDataNext
     */
    public function testNextGetType($inColorDescription, $outColorDescription)
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
            'bothDefaultColor'=>[
                'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>true]),
                'outColor'=> new ColorDescription(['type'=>'outside', 'is_default'=>true]),
                'result'=>'default'
            ]
        ];
    }

    public function getDataNext()
    {
        return [
            'only-one-default'=>[
                'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>false]),
                'outColor'=> new ColorDescription(['type'=>'outside', 'is_default'=>true]),
            ],
            'none-default'=>[
            'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>false]),
            'outColor'=> new ColorDescription(['type'=>'outside', 'is_default'=>false]),
    ]
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
        $this->model = new DefaultColorProcessor($this->next, $objectFactory);
    }
}