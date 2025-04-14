<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Spi;

use Magento\Framework\App\RequestInterface;

interface ContextFactoryInterface
{
    /**
     * @param RequestInterface $request
     * @return ContextInterface
     */
    public function create(RequestInterface $request) :ContextInterface;
}
