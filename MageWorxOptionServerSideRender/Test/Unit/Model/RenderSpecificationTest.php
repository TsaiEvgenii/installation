<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model;

use BelVG\MageWorxOptionServerSideRender\Api\AdditionalRenderActionInterface;
use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;
use BelVG\MageWorxOptionServerSideRender\Api\SpecificationInterface;
use BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class RenderSpecificationTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var RenderSpecification
     */
    private $model;
    /**
     * @var SpecificationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $specification;
    /**
     * @var AdditionalRenderActionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $additionalActionMock;

    public function testSetAdditionalActions()
    {
        $block  = $this->getMockBuilder(RenderBlockInterface::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->additionalActionMock->expects(self::once())
                                  ->method('process')
                                  ->with($block);
        $this->model->setAdditionalActions($block);
    }

    /**
     * @dataProvider getSpecificationDataProvider
     */
    public function testIsSpecifiedBy($specification, $expected)
    {
        $option  = $this->getMockBuilder(ProductCustomOptionInterface::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->specification->method('isSpecifiedBy')
                           ->with($option)
                           ->willReturn($specification);
        $result = $this->model->isSpecifiedBy($option);
        $this->assertSame($expected, $result);
    }

    public function testGetBlock()
    {
        $model = new RenderSpecification($this->specification, $this->additionalActionMock, 'BlockName', '');
        $this->assertSame('BlockName', $model->getBlock());
        $model = new RenderSpecification($this->specification, $this->additionalActionMock, 'BlockNameA', '');
        $this->assertSame('BlockNameA', $model->getBlock());
    }

    public function testGetTemplate()
    {
        $this->model->getTemplate();
        $model = new RenderSpecification($this->specification, $this->additionalActionMock, '', 'template');
        $this->assertSame('template', $model->getTemplate());
        $model = new RenderSpecification($this->specification, $this->additionalActionMock, '', 'templateA');
        $this->assertSame('templateA', $model->getTemplate());
    }

    public function getSpecificationDataProvider()
    {
        yield 'false-result'=>[
            'specification'=>false,
            'expected'=>false
        ];

        yield 'true-result'=>[
            'specification'=>true,
            'expected'=>true
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->specification  = $this->getMockBuilder(SpecificationInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->additionalActionMock  = $this->getMockBuilder(AdditionalRenderActionInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new RenderSpecification($this->specification, $this->additionalActionMock, '', '');
    }
}
