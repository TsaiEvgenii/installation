<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Service\HtmlProductOptionParserParser;
use BelVG\MageWorxOptionServerSideRender\Model\Service\Parser;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class HtmlDivOptionTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    private \Laminas\Stdlib\StringWrapper\MbString $mbString;

    /**
     * @var Parser
     */
    private Parser $model;

    public function getData()
    {
        return [
            'result-wrapper-div'=>[
                'result'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/WrapperButton/input/many_option.html'),
                'expected'=>'
        Udvendig farve
    '
            ],
            'result-wrapper-div-2'=>[
                'result'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/WrapperButton/input/many_option_2.html'),
                'expected'=>'
        Udvendig farve tests lenta
    '],
            'empty-wrapper-div-2'=>[
                'result'=>\file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'__files/renderedOptions/WrapperButton/input/empty.html'),
                'expected'=>'']
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->mbString = new \Laminas\Stdlib\StringWrapper\MbString();
        $this->model = new Parser($this->mbString, '//div');
    }

    /**
     * @param $result
     * @param $expected
     * @dataProvider getData
     */
    public function testGet($result, $expected)
    {
       $generator = $this->model->get($result);
       foreach ($generator as $div){
           $this->assertSame($expected, $div->getElementsByTagName('label')[0]->textContent);
           break;
       }
    }
}
