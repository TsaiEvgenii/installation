<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service\Test\Unit;

use BelVG\MageWorxOptionServerSideRender\Model\Dto\SelectedOption;
use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedRequestOptionService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class SelectedRequestOptionServiceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var SelectedRequestOptionService
     */
    private $model;

    /**
     * @dataProvider getData
     */
    public function testGet($requestData, $expected)
    {
        $result = $this->model->get($requestData);
        foreach ($result as $item) {
            $this->assertInstanceOf(SelectedOptionValue::class, $item->getObjectValue());
        }
        $this->assertEquals($expected, $result);
    }

    public function getData()
    {
        return [
            'empty-request'=>[
                'request'=>[],
                'expected'=>[]
            ],
            'default-width-height-options'=>[
                'request'=>['options_51674'=>'40', 'options_51675'=>'41'],
                'expected'=>[new SelectedOption(['option_id'=>51674, 'value'=>'40']),
                    new SelectedOption(['option_id'=>51675, 'value'=>'41'])]
            ],
            'default-additional-options'=>[
                'request'=>['options-51674'=>'40', 'options-51675'=>'41'],
                'expected'=>[new SelectedOption(['option_id'=>51674, 'value'=>'40']),
                    new SelectedOption(['option_id'=>51675, 'value'=>'41'])]
            ],

            'some-another-options'=>[
                'request'=>['options'=>'40', 'options75'=>'41'],
                'expected'=>[]
            ],
            'int-option'=>[
                'request'=>[12=>'40', 13=>'41'],
                'expected'=>[new SelectedOption(['option_id'=>12, 'value'=>'40']),
                    new SelectedOption(['option_id'=>13, 'value'=>'41'])]
            ],
            'int-00000-option'=>[
                'request'=>[12121=>'40', 13131=>'41'],
                'expected'=>[new SelectedOption(['option_id'=>12121, 'value'=>'40']),
                    new SelectedOption(['option_id'=>13131, 'value'=>'41'])]
            ],
            'int-00000000-option'=>[
                'request'=>[121211212=>'40', 13131125123=>'41'],
                'expected'=>[new SelectedOption(['option_id'=>121211212, 'value'=>'40']),
                    new SelectedOption(['option_id'=>13131125123, 'value'=>'41'])]
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->model = $this->objectManager->getObject(SelectedRequestOptionService::class);
    }
}
