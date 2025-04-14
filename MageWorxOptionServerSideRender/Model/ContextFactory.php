<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model;

use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextFactoryInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextResolveInterface;
use Magento\Framework\App\RequestInterface;

class ContextFactory implements ContextFactoryInterface
{
    private ContextResolveInterface $defaultResolver;
    /**
     * @var ContextResolveInterface[]
     */
    private array $resolvers;

    /**
     * @param ContextResolveInterface $defaultResolver
     * @param ContextResolveInterface[] $resolvers
     */
    public function __construct(ContextResolveInterface $defaultResolver, $resolvers = [])
    {
        $this->defaultResolver = $defaultResolver;
        $this->resolvers = $resolvers;
    }

    public function create(RequestInterface $request): ContextInterface
    {
        $resolvers = \array_filter($this->resolvers, fn($resolve)=>($resolve->isApplicable($request)));
        foreach ($resolvers as $resolve) {
            return $resolve->createContext($request);
        }
        return  $this->defaultResolver->createContext($request);
    }
}
