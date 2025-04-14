<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextFactoryInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\SelectedRequestOptionInterface;
use Magento\Framework\App\RequestInterface;

class GetSelectedOptions implements \IteratorAggregate
{
    private SelectedRequestOptionInterface $selectedRequestOption;
    private ?ContextInterface $context = null;
    private ContextFactoryInterface $contextFactory;
    private RequestInterface $request;

    /**
     * GetSelectedOptions constructor.
     * @param SelectedRequestOptionInterface $selectedRequestOption
     * @param ContextFactoryInterface $contextFactory
     * @param RequestInterface $request
     */
    public function __construct(
        SelectedRequestOptionInterface $selectedRequestOption,
        ContextFactoryInterface $contextFactory,
        RequestInterface $request
    ) {
        $this->selectedRequestOption = $selectedRequestOption;
        $this->contextFactory = $contextFactory;
        $this->request = $request;
    }

    /**
     * @return SelectedOptionInterface[]|iterable
     */
    public function get()
    {
        $context = $this->getContext();
        return $this->selectedRequestOption->get($context->getParams());
    }

    public function getIterator() :\Traversable
    {
        return new \ArrayIterator($this->get());
    }

    private function getContext()
    {
        if ($this->context === null) {
            $this->context = $this->contextFactory->create($this->request);
        }
        return $this->context;
    }
}
