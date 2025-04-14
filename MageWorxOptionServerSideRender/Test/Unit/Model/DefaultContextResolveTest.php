<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model;

use BelVG\MageWorxOptionServerSideRender\Model\Context;
use BelVG\MageWorxOptionServerSideRender\Model\DefaultContextResolve;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class DefaultContextResolveTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var ObjectFactory|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $objectFactoryMock;

    /**
     * @var DefaultContextResolve
     */
    private $model;
    /**
     * @var RequestInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    public function testCreateContext()
    {
        $context  = $this->getMockBuilder(Context::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->objectFactoryMock->method('create')
                                ->with(Context::class, ['request'=>$this->requestMock])
                                ->willReturn($context);
        $this->assertSame($context, $this->model->createContext($this->requestMock));
    }

    public function testIsApplicable()
    {
        $result = $this->model->isApplicable($this->requestMock);
        $this->assertTrue($result);
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->objectFactoryMock = $this->getMockBuilder(ObjectFactory::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->requestMock  = $this->getMockBuilder(RequestInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new DefaultContextResolve($this->objectFactoryMock);
    }
}
