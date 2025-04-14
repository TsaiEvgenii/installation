<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\InsideOutsideColorPrice\Model\OptionPriceCalculator;
use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use BelVG\LayoutCustomizer\Model\Layout;
use BelVG\MageWorxOptionServerSideRender\Model\Dto\ColorDescription;
use BelVG\MageWorxOptionServerSideRender\Model\Dto\SelectedOption;
use BelVG\MageWorxOptionServerSideRender\Model\Service\CalculateColorPrice;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetInColorService;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetOutColorService;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ColorfulWindowTypeProcessorInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\GetProductLayoutInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CalculateColorPriceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    private  $model;
    private  $selectedOptions;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|GetInColorService
     */
    private  $getInColorService;
    /**
     * @var GetOutColorService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $getOutColorService;
    /**
     * @var ColorfulWindowTypeProcessorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $colorfulWindowTypeProcessor;
    /**
     * @var Product|\PHPUnit\Framework\MockObject\MockObject
     */
    private $product;
    /**
     * @var Option|\PHPUnit\Framework\MockObject\MockObject
     */
    private $option;
    /**
     * @var Value|\PHPUnit\Framework\MockObject\MockObject
     */
    private $optionValue;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private $logger;
    /**
     * @var GetProductLayoutInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $getProductLayout;
    /**
     * @var OptionPriceCalculator|\PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutPriceCalculator;
    private $basePrice = 0;

    public function testGetCustomPrice()
    {
        $this->basePrice = 100;
        $colorInDescription = new ColorDescription(['title'=>'qwerty inside', 'is_default'=>true, 'type'=>'inside']);
        $colorOutDescription = new ColorDescription(['title'=>'qwerty outside', 'is_default'=>false, 'type'=>'outside']);
        $this->initMocks($colorInDescription, $colorOutDescription);
        $this->logger->expects(self::never())
                     ->method('warning');
        $this->assertSame(225,
            $this->model->getCustomPrice($this->product, $this->option, $this->optionValue, $this->basePrice));
    }

    protected function initMocks($colorInDescription, $colorOutDescription){
        $type = 'default';
        $basePrice = $this->basePrice;
        $layout  = $this->getMockBuilder(LayoutInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $selectedOptions = [new SelectedOption(['option_id'=>1,'value'=>121]), new SelectedOption(['option_id'=>3,'value'=>123])];
        $this->getProductLayout->expects(self::once())
                               ->method('get')
                               ->with($this->product)
                               ->willReturn($layout);
        $option_1  = $this->getMockBuilder(Option::class)
            ->disableOriginalConstructor()
            ->getMock();
        $option_2  = $this->getMockBuilder(Option::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->product->method('getOptions')
            ->willReturn([$option_1, $option_2]);
        $this->selectedOptions->expects(self::once())
            ->method('get')
            ->willReturn($selectedOptions);
        $this->getInColorService->expects(self::once())
            ->method('get')
            ->with([$option_1, $option_2], $selectedOptions)
            ->willReturn($colorInDescription);
        $this->getOutColorService->expects(self::once())
            ->method('get')
            ->with([$option_1, $option_2], $selectedOptions)
            ->willReturn($colorOutDescription);
        $this->colorfulWindowTypeProcessor->expects(self::once())
            ->method('getType')
            ->with($colorInDescription, $colorOutDescription)
            ->willReturn($type);
        $this->layoutPriceCalculator->expects(self::once())
                                    ->method('resolvePrice')
                                    ->with($type, $basePrice, $layout)
                                    ->willReturnCallback(fn($type, $basePrice, $layout)=> $basePrice + 125);
    }

    /**
     * @dataProvider getDataForLogging
     */
    public function testLoggingEmptyColors($colorInDescription, $colorOutDescription)
    {
        $this->initMocks($colorInDescription, $colorOutDescription);
        $this->product->method('getId')
                      ->willReturn('12');
        $this->logger->expects(self::once())
                    ->method('warning');
        $basePrice = 0;
        $this->assertSame(125,
            $this->model->getCustomPrice($this->product, $this->option, $this->optionValue, $basePrice));
    }

    public function getDataForLogging()
    {
        return [
            'bothEmptyColor'=>[
                'inColor'=>new ColorDescription(['title'=>'', 'is_default'=>true, 'type'=>'inside']),
                'outColor'=>new ColorDescription(['title'=>'', 'type'=>'outside'])
            ],
            'outColorEmpty'=>[
                'inColor'=>new ColorDescription(['title'=>'Exists', 'is_default'=>true, 'type'=>'inside']),
                'outColor'=>new ColorDescription(['title'=>'', 'type'=>'outside'])
            ],
            'inColorEmpty'=>[
                'inColor'=>new ColorDescription(['title'=>'', 'is_default'=>true, 'type'=>'inside']),
                'outColor'=>new ColorDescription(['title'=>'Exists', 'type'=>'outside'])
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->product  = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->option  = $this->getMockBuilder(Option::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->optionValue  = $this->getMockBuilder(Value::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectedOptions  = $this->getMockBuilder(GetSelectedOptions::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->getInColorService  = $this->getMockBuilder(GetInColorService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->getOutColorService  = $this->getMockBuilder(GetOutColorService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->colorfulWindowTypeProcessor  = $this->getMockBuilder(ColorfulWindowTypeProcessorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger  = $this->getMockBuilder(LoggerInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->getProductLayout  = $this->getMockBuilder(GetProductLayoutInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->layoutPriceCalculator  = $this->getMockBuilder(OptionPriceCalculator::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new CalculateColorPrice($this->selectedOptions,
            $this->getInColorService,
            $this->getOutColorService,
            $this->colorfulWindowTypeProcessor,
            $this->logger,
            $this->getProductLayout,
            $this->layoutPriceCalculator
        );

    }
}
