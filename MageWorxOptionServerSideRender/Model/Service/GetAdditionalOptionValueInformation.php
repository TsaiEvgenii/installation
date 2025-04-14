<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionFeatures\Model\Image as ImageModel;
use MageWorx\OptionFeatures\Model\ResourceModel\Image\Collection as ImagesCollection;
use MageWorx\OptionFeatures\Model\ResourceModel\Image\CollectionFactory;

class GetAdditionalOptionValueInformation
{
    const DEFAULT_VALUE = 0;
    private CollectionFactory $imageCollectionFactory;
    private Helper $helper;

    /**
     * GetAdditionalOptionValueInformation constructor.
     * @param CollectionFactory $imageCollectionFactory
     */
    public function __construct(
        CollectionFactory  $imageCollectionFactory,
        Helper $helper
    ) {
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->helper = $helper;
    }
    public function get($value, int $storeId) :array
    {
        $additionalInformation = [];
        /** @var ImagesCollection $collection */
        $collection = $this->imageCollectionFactory
            ->create()
            ->addFieldToFilter(
                'mageworx_option_type_id',
                $value->getData('mageworx_optiontemplates_group_option_type_id')
            );
        $collection->addFieldToFilter('store_id',[['eq'=>self::DEFAULT_VALUE],['eq'=>$storeId]]);
        $additionalInformation['sort_order'] = $value->getSortOrder();
        $itemsByStore = $this->getItemsByStore($collection,$storeId);
        $defaultItems = $this->getItemsByStore($collection,self::DEFAULT_VALUE);
        $items = count($itemsByStore) > 0 ? $itemsByStore : $defaultItems;
        foreach ($items as $collectionItem) {
            $additionalInformation['images'][$collectionItem->getOptionTypeImageId()] = [
                'value_id' => $collectionItem->getOptionTypeImageId(),
                'option_type_id' => $collectionItem->getMageworxOptionTypeId(),
                'position' => $collectionItem->getSortOrder(),
                'file' => $collectionItem->getValue(),
                'label' => $collectionItem->getTitleText(),
                'custom_media_type' => $collectionItem->getMediaType(),
                'color' => $collectionItem->getColor(),
                ImageModel::COLUMN_HIDE_IN_GALLERY =>
                    $collectionItem->getData(ImageModel::COLUMN_HIDE_IN_GALLERY),
                'url' => $this->helper->getThumbImageUrl(
                    $collectionItem->getValue(),
                    Helper::IMAGE_MEDIA_ATTRIBUTE_BASE_IMAGE
                ),
                ImageModel::COLUMN_REPLACE_MAIN_GALLERY_IMAGE =>
                    $collectionItem->getData(ImageModel::COLUMN_REPLACE_MAIN_GALLERY_IMAGE),
            ];
            if ($collectionItem->getData(ImageModel::COLUMN_REPLACE_MAIN_GALLERY_IMAGE)) {
                $additionalInformation['images'][$collectionItem->getOptionTypeImageId()]['full'] =
                    $this->getImageUrl($collectionItem->getValue());
                $additionalInformation['images'][$collectionItem->getOptionTypeImageId()]['img'] =
                    $this->getImageUrl($collectionItem->getValue());
                $additionalInformation['images'][$collectionItem->getOptionTypeImageId()]['thumb'] =
                    $this->getImageUrl($collectionItem->getValue());
            }
        }
        return $additionalInformation;
    }

    /**
     * Get image url for specified type, width or height
     *
     * @param $path
     * @param null $type
     * @param null $height
     * @param null $width
     * @return string
     */
    private function getImageUrl($path)
    {
        if (!$path) {
            return '';
        }
        return $this->helper->getImageUrl($path);
    }

    private function getItemsByStore(ImagesCollection $collection, int $storeId)
    {
        return \array_filter($collection->getItems(), fn($item)=> (int)$item->getData('store_id') === $storeId);
    }
}
