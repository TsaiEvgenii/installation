<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model;

use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class SelectedOptionValueTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @dataProvider getData
     */
    public function testGetValue($value, $expected)
    {
        $model = new SelectedOptionValue(['value'=>$value]);
        $this->assertSame($expected, $model->getValue());
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider getDataToConvertToString
     */
    public function testGetValueConvertToString($value, $expected)
    {
        $model = new SelectedOptionValue(['value'=>$value]);
        $this->assertSame($expected, (string)$model);
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider getRalValueProvider
     */
    public function testGetRalValue($value, $expected)
    {
    }

    public function getData()
    {
        return [
            'emptyString'=>[
                'value' => '',
                'expected' => ''
            ],
            'stringvalue'=>[
                'value' => '123456',
                'expected' => '123456'
            ],
            'special-color-value'=>[
                'value' => '5285660:ral_1000',
                'expected' => '5285660:ral_1000'
            ],
            'set-int-value'=>[
                'value'=>12,
                'expected'=>'12'
            ]
        ];
    }

    public function getDataToConvertToString()
    {
        return [
            'emptyString'=>[
                'value' => '',
                'expected' => ''
            ],
            'stringvalue'=>[
                'value' => '123456',
                'expected' => '123456'
            ],
            'special-color-value'=>[
                'value' => '5285660:ral_1000',
                'expected' => '5285660'
            ],
        ];
    }

    public function getRalValueProvider()
    {
        return [
            'special-color-value'=>[
                'value' => '5285660:ral_1000',
                'expected' => 'ral_1000'
            ],
            'special-color-value-2'=>[
                'value' => '5285660:ral_100000',
                'expected' => 'ral_100000'
            ],
            'invalid_ral_value'=>[
                'value' => '5285660:ra_100000',
                'expected' => 'ra_100000'
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }
}
