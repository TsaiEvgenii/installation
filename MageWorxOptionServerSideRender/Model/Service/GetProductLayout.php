<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\GetProductLayoutInterface;
use Magento\Catalog\Model\Product;
use BelVG\LayoutCustomizer\Helper\Data;

class GetProductLayout implements GetProductLayoutInterface
{
    private LayoutRepositoryInterface $layoutRepository;

    /**
     * GetProductLayout constructor.
     * @param LayoutRepositoryInterface $layoutRepository
     */
    public function __construct(LayoutRepositoryInterface $layoutRepository)
    {
        $this->layoutRepository = $layoutRepository;
    }

    /**
     * @param Product $product
     * @return LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(Product $product): LayoutInterface
    {
        return $this->layoutRepository->getById($product->getData(Data::PRODUCT_LAYOUT_ATTR));
    }
}
