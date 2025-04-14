<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface;
use BelVG\LayoutCustomizer\Helper\Data;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetProductLayout;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class GetProductLayoutTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var Product|\PHPUnit\Framework\MockObject\MockObject
     */
    private $product;
    /**
     * @var LayoutRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutRepository;

    private $model;

    /**
     * @dataProvider getData
     */
    public function testGet($layoutId, $expected)
    {
        $this->product->method('getData')
                      ->with(Data::PRODUCT_LAYOUT_ATTR)
                      ->willReturn($layoutId);
        $layout  = $this->getMockBuilder(LayoutInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->layoutRepository->method('getById')
                               ->with($expected)
                               ->willReturn($layout);
        $this->assertSame($layout, $this->model->get($this->product));
    }

    public function getData()
    {
        return [
            'layout-id-1'=>[
                'layout_id'=>1,
                'expected'=>1
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->product  = $this->getMockBuilder(Product::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->layoutRepository  = $this->getMockBuilder(LayoutRepositoryInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new GetProductLayout($this->layoutRepository);
    }
}
