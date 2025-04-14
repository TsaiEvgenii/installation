<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model;

use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextResolveInterface;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\App\RequestInterface;

class DefaultContextResolve implements ContextResolveInterface
{
    private ObjectFactory $objectFactory;

    /**
     * @param ObjectFactory $objectFactory
     */
    public function __construct(ObjectFactory $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    public function isApplicable(RequestInterface $request): bool
    {
        return true;
    }

    public function createContext(RequestInterface $request) : Context
    {
        return $this->objectFactory->create(Context::class, ['request'=>$request]);
    }
}
