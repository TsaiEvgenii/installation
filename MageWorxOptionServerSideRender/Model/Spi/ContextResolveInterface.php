<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Spi;

use Magento\Framework\App\RequestInterface;

interface ContextResolveInterface
{
    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function isApplicable(RequestInterface $request) :bool;

    /**
     * @param RequestInterface $request
     * @return ContextInterface
     */
    public function createContext(RequestInterface $request) :ContextInterface;
}
