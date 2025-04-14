<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Option\Type;

use BelVG\MageWorxOptionServerSideRender\Model\ProductRegistry;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetAdditionalOptionValueInformation;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\HtmlProductOptionParserParser;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedRequestOptionService;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\PriceDiscountInterface;
use BelVG\ProductPriceDisplay\Pricing\Render\Amount;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\ValueFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class RadioTest
 * @package BelVG\MageWorxOptionServerSideRender\Block\Option\Type
 */
class RadioTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var Radio|\PHPUnit\Framework\MockObject\MockObject
     */
    private $model;
    private \Laminas\Stdlib\StringWrapper\MbString $mbString;

    /**
     * @dataProvider getData
     */
    public function testProcess($result, $toHtml, $option, $valueIds,  $expected)
    {
        $this->model->method('getOption')
                    ->willReturn($option);
        $model = $this->model;
        $self = $this;
        $this->model->method('toHtml')
                    ->willReturnCallback(function () use( $toHtml, $model, $valueIds, $self ){
                        static $index = 0;
                        $self::assertEquals($model->getValueId(), $valueIds[$index][0]);
                        $index++;
                        return $toHtml; });
        $result = $this->model->process($result);
        $this->assertSame($expected, $result);
    }

    public function getData()
    {
        return [
            'empty-result'=>[
                'result'=>'',
                'toHtml'=>'',
                'option'=>$this->createOptionMock(),
                'valueIds'=>[],
                'expected'=>''
            ],
            'result-nochange'=>[
                'result'=>'result',
                'toHtml'=>'',
                'option'=>$this->createOptionMock(),
                'valueIds'=>[],
                'expected'=>'result'
            ],
            'result-for-one-option'=>[
                'result'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/RadioButton/input/one_option.html'),
                'toHtml'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/RadioButton/output/toHtml/one_option.html'),
                'option'=>$this->createOptionMock(),
                'valueIds'=>[['319088']],
                'expected'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/RadioButton/output/one_option.html')
            ],
            'result-for-many-option'=>[
                'result'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/RadioButton/input/many_option.html'),
                'toHtml'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/RadioButton/output/toHtml/one_option.html'),
                'option'=>$this->createOptionMock(),
                'valueIds'=>[['2131806'],['319088'],['319089'],['319090'], ['1863136'], ['1924650'],['1924651'], ['1924652'], ['1924653'],['1924654'],['1989838'],['1989839']],
                'expected'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/RadioButton/output/many_option.html.text')
            ]
        ];
    }


    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->mbString = new \Laminas\Stdlib\StringWrapper\MbString();
        $parser = new HtmlProductOptionParserParser($this->mbString);
        $logger  = $this->getMockBuilder(LoggerInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $amount  = $this->getMockBuilder(Amount::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $valueFactory  = $this->getMockBuilder(ValueFactory::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $imageCommander  = $this->getMockBuilder(GetAdditionalOptionValueInformation::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $context  = $this->getMockBuilder(\Magento\Framework\View\Element\Template\Context::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $request  = $this->getMockBuilder( RequestInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $request->method('getParams')
                ->willReturn([]);
        $context->method('getRequest')
                ->willReturn($request);
        $helperData  = $this->getMockBuilder(\Magento\Framework\Pricing\Helper\Data::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $priceDiscountMock  = $this->getMockBuilder(PriceDiscountInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $selectedOptions   = $this->getMockBuilder(GetSelectedOptions::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model  = $this->getMockBuilder(Radio::class)
                        ->setConstructorArgs([$context,
                                              $imageCommander,
                                              $parser,
                                              $logger,
                                              $amount,
                                              $valueFactory,
                                              $helperData,
                                              $priceDiscountMock,
                                              $selectedOptions,
                                              []])
                        ->onlyMethods(['toHtml'])
                        ->addMethods(['getOption'])
                        ->getMock();
    }

    private function createOptionMock($data = [])
    {
        $optionMock  = $this->getMockBuilder(Option::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        return $optionMock;
    }
}
