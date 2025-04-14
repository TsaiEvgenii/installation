<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedRequestOptionService;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextFactoryInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class GetSelectedOptionsTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var ContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    private $model;
    /**
     * @var GetSelectedOptions|\PHPUnit\Framework\MockObject\MockObject
     */
    private $selectedRequestOption;
    /**
     * @var ContextFactoryInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextFactoryMock;
    /**
     * @var RequestInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    /**
     * @dataProvider getData
     */
    public function testGetIterator($withGet, $return, $expected)
    {
        $this->contextMock->method('getParams')
            ->willReturn($withGet);
        $this->selectedRequestOption->method('get')
            ->with($withGet)
            ->willReturn($return);
        foreach ($this->model as $index => $value) {
            $this->assertSame($expected[$index], $value);
            unset($expected[$index]);
        }
        self::assertEmpty($expected);
    }

    /**
     * @dataProvider getData
     */
    public function testGet($withGet, $return, $expected)
    {
        $this->contextMock->method('getParams')
                          ->willReturn($withGet);
        $this->selectedRequestOption->method('get')
                                    ->with($withGet)
                                    ->willReturn($return);
        $result = $this->model->get();
        $this->assertSame($expected, $result);
    }

    public function getData()
    {
        return [
            'empty-array'=>[
                'withGet'=>['12'=>12],
                'return'=>[],
                'expected'=>[]
            ],
            'array'=>[
                'withGet'=>['12'=>12],
                'return'=>['12',12],
                'expected'=>['12',12]
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->selectedRequestOption  = $this->getMockBuilder(SelectedRequestOptionService::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->contextMock  = $this->getMockBuilder(ContextInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->contextFactoryMock  = $this->getMockBuilder(ContextFactoryInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->requestMock  = $this->getMockBuilder(RequestInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->contextFactoryMock->method('create')
                                 ->with($this->requestMock)
                                 ->willReturn($this->contextMock);
        $this->model = new GetSelectedOptions($this->selectedRequestOption, $this->contextFactoryMock, $this->requestMock);
    }
}
