<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\AdditionalRenderActionInterface;
use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;
use BelVG\MageWorxOptionServerSideRender\Api\SpecificationInterface;
use BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification;
use BelVG\MageWorxOptionServerSideRender\Model\Service\CreateBlockService;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

class CreateBlockServiceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var CreateBlockService
     */
    private $model;
    /**
     * @var LayoutInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutViewMock;
    /**
     * @var RenderBlockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $blockMock;
    /**
     * @var SpecificationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $specificationA;
    /**
     * @var SpecificationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $specificationB;
    /**
     * @var RenderSpecification|\PHPUnit\Framework\MockObject\MockObject
     */
    private $renderSpecificationA;
    /**
     * @var RenderSpecification|\PHPUnit\Framework\MockObject\MockObject
     */
    private $renderSpecificationB;
    /**
     * @var RenderSpecification|\PHPUnit\Framework\MockObject\MockObject
     */
    private $defaultSpecification;
    /**
     * @var AdditionalRenderActionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $additionalAction;

    public function testCreateBlock()
    {
        $option  = $this->getMockBuilder(ProductCustomOptionInterface::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->layoutViewMock->method('createBlock')
                            ->with('blockA', '', ['data'=>['option'=>$option]])
                            ->willReturn($this->blockMock);
        $this->blockMock->expects(self::once())
                       ->method('setTemplate')
                       ->with('templateA');
        $this->specificationA->method('isSpecifiedBy')
                            ->with($option)
                            ->willReturn(true);
        $this->renderSpecificationA->expects(self::once())
                                  ->method('setAdditionalActions');

        $this->specificationB->expects(self::never())
                            ->method('isSpecifiedBy');
        $this->model->createBlock($option);
    }

    public function testCreateBlockSpecificationB()
    {
        $option  = $this->getMockBuilder(ProductCustomOptionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->layoutViewMock->method('createBlock')
            ->with('blockB', '', ['data'=>['option'=>$option]])
            ->willReturn($this->blockMock);
        $this->blockMock->expects(self::once())
            ->method('setTemplate')
            ->with('templateB');
        $this->specificationA->method('isSpecifiedBy')
            ->with($option)
            ->willReturn(false);
        $this->specificationB->expects(self::once())
            ->method('isSpecifiedBy')
            ->with($option)
            ->willReturn(true);
        $this->renderSpecificationB->expects(self::once())
            ->method('setAdditionalActions');

        $this->renderSpecificationA->expects(self::never())
            ->method('setAdditionalActions');
        $this->model->createBlock($option);
    }

    public function testCreateBlockByDefaultSpecification()
    {
        $option  = $this->getMockBuilder(ProductCustomOptionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->layoutViewMock->method('createBlock')
            ->with('defaultBlock', '', ['data'=>['option'=>$option]])
            ->willReturn($this->blockMock);
        $this->blockMock->expects(self::once())
            ->method('setTemplate')
            ->with('defaultTempalate');
        $this->specificationA->method('isSpecifiedBy')
            ->with($option)
            ->willReturn(false);
        $this->specificationB->expects(self::once())
            ->method('isSpecifiedBy')
            ->with($option)
            ->willReturn(false);
        $this->renderSpecificationB->expects(self::never())
            ->method('setAdditionalActions');

        $this->renderSpecificationA->expects(self::never())
            ->method('setAdditionalActions');
        $this->defaultSpecification->expects(self::once())
                                   ->method('setAdditionalActions');
        $this->model->createBlock($option);
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->layoutViewMock  = $this->getMockBuilder(LayoutInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->blockMock  = $this->getMockBuilder(RenderBlockInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->specificationA  = $this->getMockBuilder(SpecificationInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->specificationB  = $this->getMockBuilder(SpecificationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->additionalAction  = $this->getMockBuilder(AdditionalRenderActionInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->renderSpecificationA  = $this->getMockBuilder(RenderSpecification::class)
                                            ->setConstructorArgs([$this->specificationA, $this->additionalAction,'blockA', 'templateA'])
                                            ->onlyMethods(['setAdditionalActions'])
                                            ->getMock();
        $this->renderSpecificationB  = $this->getMockBuilder(RenderSpecification::class)
            ->setConstructorArgs([$this->specificationB, $this->additionalAction, 'blockB', 'templateB'])
            ->onlyMethods(['setAdditionalActions'])
            ->getMock();
        $this->defaultSpecification = $this->getMockBuilder(RenderSpecification::class)
            ->setConstructorArgs([$this->specificationA, $this->additionalAction, 'defaultBlock', 'defaultTempalate'])
            ->onlyMethods(['setAdditionalActions'])
            ->getMock();
        $this->model = new CreateBlockService(
            $this->layoutViewMock,
            $this->defaultSpecification,
            [$this->renderSpecificationA, $this->renderSpecificationB]
        );
    }
}
