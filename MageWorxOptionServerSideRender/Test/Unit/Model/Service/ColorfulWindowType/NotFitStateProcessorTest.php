<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service\ColorfulWindowType;

use BelVG\MageWorxOptionServerSideRender\Exception\NotFitColorfulTypeException;
use BelVG\MageWorxOptionServerSideRender\Model\Dto\ColorDescription;
use BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType\AbstractColorProcessor;
use BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType\NotFitStateProcessor;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class NotFitStateProcessorTest extends TestCase
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
    public function testGetType($inColorDescription, $outColorDescription)
    {
        $this->expectException(NotFitColorfulTypeException::class);
        $this->model->getType($inColorDescription, $outColorDescription);
    }

    public function getData()
    {
        return [
            'inSideIsDefault'=>[
                'inColor'=> new ColorDescription(['type'=>'inside', 'is_default'=>true]),
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
        $this->next->expects(self::never())
                   ->method('getType');
        $objectFactory  = $this->getMockBuilder(ObjectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = new NotFitStateProcessor($this->next, $objectFactory);
    }
}
