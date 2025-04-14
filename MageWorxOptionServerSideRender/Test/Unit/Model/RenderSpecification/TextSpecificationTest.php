<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\RenderSpecification;

use BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification\TextSpecification;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class TextSpecificationTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var TextSpecification
     */
    private $model;
    /**
     * @var ProductCustomOptionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $optionMock;

    /**
     * @dataProvider getData
     */
    public function testIsSpecifiedBy($field, $expected)
    {
        $this->optionMock->method('getType')
                         ->willReturn($field);
        $this->assertSame($expected, $this->model->isSpecifiedBy($this->optionMock));
    }

    public function getData()
    {
        yield 'field-option'=>[
            'fieldType'=>'field',
            'expected'=>true
        ];

        yield 'select-option'=>[
            'fieldType'=>'select',
            'expected'=>false
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->optionMock  = $this->getMockBuilder(ProductCustomOptionInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new TextSpecification();
    }
}
