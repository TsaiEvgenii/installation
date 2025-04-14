<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use Magento\Framework\App\CacheInterface;

class CacheCleanService
{
    private CacheInterface $cache;

    /**
     * CacheCleanService constructor.
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    public function execute()
    {
        $this->cache->clean([\Magento\Catalog\Model\Product::CACHE_TAG]);
    }
}
