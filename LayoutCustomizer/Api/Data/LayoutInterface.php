<?php


namespace BelVG\LayoutCustomizer\Api\Data;

interface LayoutInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const IDENTIFIER = 'identifier';
    const LAYOUT_ID = 'layout_id';
    const HORIZONATAL_FRAME = 'horizontal_frame';
    const VERTICAL_FRAME = 'vertical_frame';
    const SQM_LEVEL_STEP2 = 'sqm_level_step2';
    const IS_ACTIVE = 'is_active';
    const BASE_PRICE = 'base_price';
    const SQM_PRICE = 'sqm_price';
    const SQM_PRICE_STEP2 = 'sqm_price_step2';
    const FAMILY_ID = 'family_id';
    const LAYOUTMATERIAL_ID = 'layoutmaterial_id';
    const INOUTCOLOR_PRICE_BOTH_DIFF = 'inoutcolor_price_both_diff';
    const INOUTCOLOR_PRICE_BOTH_SAME = 'inoutcolor_price_both_same';
    const INOUTCOLOR_PRICE_INSIDE_OTHERWHITE = 'inoutcolor_price_inside_otherwhite';
    const INOUTCOLOR_PRICE_OUTSIDE_OTHERWHITE = 'inoutcolor_price_outside_otherwhite';
    const STORE_ID = 'store_id';

    /**
     * Get layout_id
     * @return string|null
     */
    public function getLayoutId();

    /**
     * Set layout_id
     * @param string $layoutId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setLayoutId($layoutId);

    /**
     * Get identifier
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Set identifier
     * @param string $identifier
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setIdentifier($identifier);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \BelVG\LayoutCustomizer\Api\Data\LayoutExtensionInterface $extensionAttributes
    );

    /**
     * Get height
     * @return string|null
     */
    public function getHeight();

    /**
     * Set height
     * @param string $height
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setHeight($height);

    /**
     * Get width
     * @return string|null
     */
    public function getWidth();

    /**
     * Set width
     * @param string $width
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setWidth($width);

    /**
     * @deprecated - not used
     *
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * @deprecated - not used
     *
     * Set is_active
     * @param string $isActive
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setIsActive($isActive);

    /**
     * Get horizontal_frame
     * @return float|null
     */
    public function getHorizontalFrame();

    /**
     * Set horizontal_frame
     * @param float $horizontalFrame
     * @return $this
     */
    public function setHorizontalFrame($horizontalFrame);

    /**
     * Get vertical_frame
     * @return float|null
     */
    public function getVerticalFrame();

    /**
     * Set vertical_frame
     * @param float $verticalFrame
     * @return $this
     */
    public function setVerticalFrame($verticalFrame);

    /**
     * Get sqm_level_step2
     * @return float|null
     */
    public function getSqmLevelStep2();

    /**
     * Set sqm_level_step2
     * @param float $sqmLevelStep2
     * @return $this
     */
    public function setSqmLevelStep2($sqmLevelStep2);

    /**
     * @return float
     */
    public function getBasePrice();

    /**
     * @param $price
     * @return float
     */
    public function setBasePrice($price);

    /**
     * @return float
     */
    public function getSqmPrice();

    /**
     * @param $price
     * @return mixed
     */
    public function setSqmPrice($price);

    /**
     * @return mixed
     */
    public function getSqmPriceStep2();

    /**
     * @param $price
     * @return mixed
     */
    public function setSqmPriceStep2($price);

    /**
     * @return mixed
     */
    public function getFamilyId();

    /**
     * @param $family_id
     * @return mixed
     */
    public function setFamilyId($family_id);

    /**
     * @return mixed
     */
    public function getLayoutmaterialId();

    /**
     * @param $material_id
     * @return mixed
     */
    public function setLayoutmaterialId($material_id);



    /**
     * Get inoutcolor_price_both_diff
     * @return float|null
     */
    public function getInoutcolorPriceBothDiff();

    /**
     * Set inoutcolor_price_both_diff
     * @param float $inoutcolorPriceBothDiff
     * @return $this
     */
    public function setInoutcolorPriceBothDiff($inoutcolorPriceBothDiff);

    /**
     * Get inoutcolor_price_both_same
     * @return float|null
     */
    public function getInoutcolorPriceBothSame();

    /**
     * Set inoutcolor_price_both_same
     * @param float $inoutcolorPriceBothSame
     * @return $this
     */
    public function setInoutcolorPriceBothSame($inoutcolorPriceBothSame);

    /**
     * Get inoutcolor_price_inside_otherwhite
     * @return float|null
     */
    public function getInoutcolorPriceInsideOtherwhite();

    /**
     * Set inoutcolor_price_inside_otherwhite
     * @param float $inoutcolorPriceInsideOtherwhite
     * @return $this
     */
    public function setInoutcolorPriceInsideOtherwhite($inoutcolorPriceInsideOtherwhite);

    /**
     * Get inoutcolor_price_outside_otherwhite
     * @return float|null
     */
    public function getInoutcolorPriceOutsideOtherwhite();

    /**
     * Set inoutcolor_price_outside_otherwhite
     * @param float $inoutcolorPriceOutsideOtherwhite
     * @return $this
     */
    public function setInoutcolorPriceOutsideOtherwhite($inoutcolorPriceOutsideOtherwhite);

    /**
     * Get store_id
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId);
}
