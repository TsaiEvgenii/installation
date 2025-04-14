<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model;

use BelVG\MageWorxOptionServerSideRender\Model\Spi\ContextInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;

class Context implements ContextInterface
{
    private RequestInterface $request;
    private ProductRegistry $productRegistry;

    /**
     * Context constructor.
     * @param RequestInterface $request
     * @param ProductRegistry $productRegistry
     */
    public function __construct(
        RequestInterface $request,
        ProductRegistry  $productRegistry
    ) {
        $this->request = $request;
        $this->productRegistry = $productRegistry;
    }

    public function getParams()
    {
        if ($this->request->getActionName() === 'configure') {
            $product = $this->getProduct();
            $options = $this->fetchOptionsFromProduct($product);
            return $options;
        }
        return $this->request->getParams();
    }


    private function getProduct()
    {
        return $this->productRegistry->getProduct();
    }

    private function fetchOptionsFromProduct(Product $product)
    {
        $preConfigureValues = $product->getPreconfiguredValues();
        return is_object($preConfigureValues) ? (array)$preConfigureValues->getOptions() : [];
    }
}
