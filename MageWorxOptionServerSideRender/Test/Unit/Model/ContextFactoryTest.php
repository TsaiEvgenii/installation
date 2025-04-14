<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model;

use BelVG\MageWorxOptionServerSideRender\Model\Context;
use BelVG\MageWorxOptionServerSideRender\Model\ContextFactory;
use BelVG\MageWorxOptionServerSideRender\Model\DefaultContextResolve;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextResolveInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ContextFactoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var ContextResolveInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resolveA;
    /**
     * @var mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;
    /**
     * @var ContextResolveInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resolveB;
    /**
     * @var DefaultContextResolve|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $defaultResolve;

    private $model;
    /**
     * @var ContextInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextA;
    /**
     * @var ContextInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextB;
    /**
     * @var ContextInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $defaultContext;

    public function testCreateResolveAApplicable()
    {
        $this->resolveA->method('isApplicable')
                       ->willReturn(true);
        $context = $this->model->create($this->requestMock);
        $this->assertSame($this->contextA, $context);
    }

    public function testCreateResolveBApplicable()
    {
        $this->resolveB->method('isApplicable')
            ->willReturn(true);
        $context = $this->model->create($this->requestMock);
        $this->assertSame($this->contextB, $context);
    }

    public function testCreateResolveDefatulaApplicable()
    {
        $context = $this->model->create($this->requestMock);
        $this->assertSame($this->defaultContext, $context);
    }

    public function testCreateResolveOnlyDefaultExists()
    {
        $model = new ContextFactory($this->defaultResolve);
        $context = $model->create($this->requestMock);
        $this->assertSame($this->defaultContext, $context);
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->requestMock  = $this->getMockBuilder(RequestInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->resolveA  = $this->getMockBuilder(ContextResolveInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->contextA  = $this->getMockBuilder(ContextInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->contextB  = $this->getMockBuilder(ContextInterface::class)
                         ->disableOriginalConstructor()
                         ->getMock();
        $this->resolveB  = $this->getMockBuilder(ContextResolveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resolveB->method('createContext')
                       ->with($this->requestMock)
                       ->willReturn($this->contextB);
        $this->resolveA->method('createContext')
            ->with($this->requestMock)
            ->willReturn($this->contextA);
        $this->defaultResolve  = $this->getMockBuilder(DefaultContextResolve::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->defaultResolve->method('isApplicable')
                             ->with($this->requestMock)
                             ->willReturn(true);
        $this->defaultContext  = $this->getMockBuilder(Context::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->defaultResolve->method('createContext')
                             ->with($this->requestMock)
                             ->willReturn($this->defaultContext);
        $this->model = new ContextFactory($this->defaultResolve, [$this->resolveA, $this->resolveB]);
    }
}
