<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\Data\ColorDescriptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Dto\ColorDescription;
use BelVG\MageWorxOptionServerSideRender\Model\Dto\SelectedOption;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetInColorService;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetOutColorService;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class GetOutColorServiceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var GetInColorService
     */
    private $model;
    /**
     * @var ObjectFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $objectFactory;

    /**
     * @dataProvider getData
     */
    public function testGet($selectedOptions, $options, $with, $result)
    {
        $this->objectFactory->method('create')
                            ->with(ColorDescriptionInterface::class, $with)
                            ->willReturn($result);
        $this->assertSame($result, $this->model->get($options, $selectedOptions));
    }

    public function getData()
    {
        return [
            'empty-result'=>[
                'selectedOptions'=>[],
                'options'=>[],
                'with'=>['data'=>[]],
                'result'=> new ColorDescription()
            ],
            'result-in-color-outside'=>[
                'selectedOptions'=>[],
                'options'=>[$this->createMockOption(['inside_outside_color'=>'outside'], ['getValues'=> [$this->createMockValue(['title'=>'Title value 1', 'is_default'=>true], ['getId'=>1])
                                ]])],
                'with'=>['data'=>[ColorDescriptionInterface::COLOR_TYPE=>'outside',
                         ColorDescriptionInterface::IS_DEFAULT=>true,
                         ColorDescriptionInterface::TITLE => 'Title value 1']],
                'result'=> new ColorDescription([ColorDescriptionInterface::COLOR_TYPE=>'outside',
                                                 ColorDescriptionInterface::IS_DEFAULT=>true,
                                                 ColorDescriptionInterface::TITLE => 'Title value 1'])
            ],
            'result-in-color-outside-2'=>[
                'selectedOptions'=>[],
                'options'=>[$this->createMockOption(
                    ['inside_outside_color'=>'outside'],
                    ['getValues'=>[
                                $this->createMockValue(['title'=>'Title value 2', 'is_default'=>true], ['getId'=>1])
                            ]]
                )],
                'with'=>['data'=>[ColorDescriptionInterface::COLOR_TYPE=>'outside',
                    ColorDescriptionInterface::IS_DEFAULT=>true,
                    ColorDescriptionInterface::TITLE => 'Title value 2']],
                'result'=> new ColorDescription([ColorDescriptionInterface::COLOR_TYPE=>'outside',
                    ColorDescriptionInterface::IS_DEFAULT=>true,
                    ColorDescriptionInterface::TITLE => 'Title value 2'])
            ],
            'result-in-color-inside'=>[
                'selectedOptions'=>[],
                'options'=>[$this->createMockOption(
                    ['inside_outside_color'=>'inside'],
                    ['getValues'=>[
                        $this->createMockValue(['title'=>'Title value 2', 'is_default'=>true], ['getId'=>1])
                    ]]
                )],
                'with'=>['data'=>[]],
                'result'=> new ColorDescription()
            ],
            'result-in-color-inside-selected-option'=>[
                'selectedOptions'=>[new SelectedOption(['option_id'=>121212, 'value'=>2])],
                'options'=>[$this->createMockOption(
                    ['inside_outside_color'=>'outside'],
                    ['getValues'=>[
                        $this->createMockValue(['title'=>'Title value 2', 'is_default'=>true], ['getId'=>1]),
                        $this->createMockValue(['title'=>'Title value 3', 'is_default'=>false], ['getId'=>2])
                    ],
                     'getId'=>121212]
                )],
                'with'=>['data'=>[ColorDescriptionInterface::COLOR_TYPE=>'outside',
                    ColorDescriptionInterface::IS_DEFAULT=>false,
                    ColorDescriptionInterface::TITLE => 'Title value 3']],
                'result'=> new ColorDescription([ColorDescriptionInterface::COLOR_TYPE=>'outside',
                    ColorDescriptionInterface::IS_DEFAULT=>false,
                    ColorDescriptionInterface::TITLE => 'Title value 3'])
            ],
            'result-in-color-inside-selected-option-not-exits'=>[
                'selectedOptions'=>[new SelectedOption(['option_id'=>121212, 'value'=>200])],
                'options'=>[$this->createMockOption(
                    ['inside_outside_color'=>'outside'],
                    ['getValues'=>[
                        $this->createMockValue(['title'=>'Title value 2', 'is_default'=>true], ['getId'=>1]),
                        $this->createMockValue(['title'=>'Title value 3', 'is_default'=>false], ['getId'=>2])
                    ],
                        'getId'=>121212]
                )],
                'with'=>['data'=>[ColorDescriptionInterface::COLOR_TYPE=>'outside',
                    ColorDescriptionInterface::IS_DEFAULT=>true,
                    ColorDescriptionInterface::TITLE => 'Title value 2']],
                'result'=> new ColorDescription([ColorDescriptionInterface::COLOR_TYPE=>'outside',
                    ColorDescriptionInterface::IS_DEFAULT=>true,
                    ColorDescriptionInterface::TITLE => 'Title value 2'])
            ],
            'result-in-color-inside-selected-option-exits-manyoption'=>[
                'selectedOptions'=>[new SelectedOption(['option_id'=>121212, 'value'=>200])],
                'options'=>[$this->createMockOption(
                    ['inside_outside_color'=>'inside'],
                    ['getValues'=>[
                        $this->createMockValue(['title'=>'Title value 2', 'is_default'=>true], ['getId'=>1]),
                        $this->createMockValue(['title'=>'Title value 3', 'is_default'=>false], ['getId'=>2])
                    ],
                        'getId'=>121212]
                ),$this->createMockOption(
                            ['inside_outside_color'=>'outside'],
                            ['getValues'=>null,
                        'getId'=>121213]
                        )],
                'with'=>['data'=>[]],
                'result'=> new ColorDescription()
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->objectFactory  = $this->getMockBuilder(ObjectFactory::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new GetOutColorService($this->objectFactory);
    }

    private function createMockOption(array $data, $methods)
    {
        $excludedMethod = ['setData', 'getData', 'getTitle'];
        $option = $this->createClassObject(
            $data,
            $methods,
            $excludedMethod,
            \Magento\Catalog\Model\Product\Option::class
        );
        return $option;
    }

    public function createClassObject(array $data, $methods, $excludedMethods, $class)
    {
        $mock  = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->setMethodsExcept($excludedMethods)
            ->getMock();
        foreach ($data as $key => $value) {
            $mock->setData($key, $value);
        }
        foreach ($methods as $method => $valueData) {
            $mock->method($method)
                ->willReturn($valueData);
        }
        return $mock;
    }

    private function createMockValue(array $data, array $methods)
    {
        $excludedMethod = ['setData', 'getData', 'getTitle'];
        $value = $this->createClassObject(
            $data,
            $methods,
            $excludedMethod,
            Value::class
        );
        return $value;
    }
}
