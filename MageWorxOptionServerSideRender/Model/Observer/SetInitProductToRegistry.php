<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Observer;

use BelVG\MageWorxOptionServerSideRender\Model\ProductRegistry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetInitProductToRegistry implements ObserverInterface
{
    private ProductRegistry $productRegistry;

    /**
     * SetInitProductToRegistry constructor.
     * @param ProductRegistry $productRegistry
     */
    public function __construct(ProductRegistry $productRegistry)
    {
        $this->productRegistry = $productRegistry;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $this->productRegistry->setProduct($product);
    }
}
