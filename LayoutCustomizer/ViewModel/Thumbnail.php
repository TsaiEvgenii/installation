<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\LayoutCustomizer\ViewModel;


use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product\Gallery\ImagesConfigFactoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Thumbnail implements ArgumentInterface
{
    public function __construct(
        protected ImagesConfigFactoryInterface $imagesConfigFactory,
        protected Image $imageHelper
    ) {

    }

    public function getThumbnail($product)
    {
        $images = $product->getMediaGalleryImages()->getItems();
        $mainImage = current(array_filter($images, function ($image) use ($product) {
            return $product->getImage() == $image->getFile();
        }));

        if (!empty($images) && empty($mainImage)) {
            $mainImage = reset($images);
        }
        $mainImageUrl = $mainImage
            ?
            $mainImage->getData('small_image_url')
            :
            $this->imageHelper->getDefaultPlaceholderUrl('image');

        return $mainImageUrl;
    }
}