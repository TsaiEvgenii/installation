<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Test\Unit\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Service\CacheCleanService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class CacheCleanServiceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\App\CacheInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheMock;
    private $model;

    public function testExecute()
    {
        $this->cacheMock->expects(self::once())
                        ->method('clean')
                        ->with([\Magento\Catalog\Model\Product::CACHE_TAG]);
        $this->model->execute();
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->cacheMock  = $this->getMockBuilder(\Magento\Framework\App\CacheInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->model = new CacheCleanService($this->cacheMock);
    }
}
