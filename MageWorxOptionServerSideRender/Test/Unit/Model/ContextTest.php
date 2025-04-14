<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model;

use BelVG\MageWorxOptionServerSideRender\Model\Context;
use BelVG\MageWorxOptionServerSideRender\Model\ProductRegistry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var RequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    private $model;
    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productMock;
    private $registry;

    /**
     * @dataProvider getData
     */
    public function testGetParams($actionName, $requestParam, $preConfig, $expected)
    {
        $this->productMock->method('getPreconfiguredValues')
                          ->willReturn(new DataObject(['options'=>$preConfig]));
        $this->requestMock->method('getActionName')
                          ->willReturn($actionName);

        $this->requestMock->method('getParams')
                          ->willReturn($requestParam);
        $result = $this->model->getParams();
        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider getData
     */
    public function testGetProductPreConfigurableNull($actionName, $requestParam)
    {
        $this->productMock->method('getPreconfiguredValues')
            ->willReturn(null);
        $this->requestMock->method('getActionName')
            ->willReturn('configure');

        $this->requestMock->method('getParams')
            ->willReturn($requestParam);
        $result = $this->model->getParams();
        $this->assertEmpty($result);
    }

    /**
     * @param $actionName
     * @param $requestParam
     * @param $preConfig
     * @param $expected
     * @dataProvider getDataProductNotExists
     */
    public function testGetParamsProductNotExist($actionName, $requestParam, $expected)
    {
        $this->requestMock->method('getActionName')
            ->willReturn($actionName);

        $this->requestMock->method('getParams')
            ->willReturn($requestParam);
        $productRegistry = new ProductRegistry();
        $this->expectException(\Error::class);
        $model = new Context($this->requestMock, $productRegistry);
        $result = $model->getParams();
        $this->assertSame($expected, $result);
    }

    public function getData()
    {
        return [
            'view'=>[
                'action-name'=>'view',
                'request-params'=>[12=>11,11=>10],
                'preConfig'=>[],
                'expected'=>[12=>11,11=>10]
            ],
            'view-configure'=>[
                'action-name'=>'configure',
                'request-params'=>[12=>11,11=>10],
                'preConfig'=>[],
                'expected'=>[]
            ],
            'view-configure-not-empty'=>[
                'action-name'=>'configure',
                'request-params'=>[12=>11,11=>10],
                'preConfig'=>[11=>13, 14=>20],
                'expected'=>[11=>13, 14=>20]
            ],
            'view-empty'=>[
                'action-name'=>'view',
                'request-params'=>[],
                'preConfig'=>[11=>13, 14=>20],
                'expected'=>[]
            ]
        ];
    }

    public function getDataProductNotExists()
    {
        return [
            'view-empty'=>[
                'action-name'=>'configure',
                'request-params'=>[],
                'expected'=>[]
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->requestMock  = $this->getMockBuilder(RequestInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->productMock  = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['getPreconfiguredValues'])
                        ->getMock();
        $this->registry = new ProductRegistry();
        $this->registry->setProduct($this->productMock);
        $this->model = new Context($this->requestMock, $this->registry);
    }
}
