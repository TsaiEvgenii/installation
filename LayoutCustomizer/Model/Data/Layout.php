<?php


namespace BelVG\LayoutCustomizer\Model\Data;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;

class Layout extends \Magento\Framework\Api\AbstractExtensibleObject implements LayoutInterface
{

    /**
     * Get layout_id
     * @return string|null
     */
    public function getLayoutId()
    {
        return $this->_get(self::LAYOUT_ID);
    }

    /**
     * Set layout_id
     * @param string $layoutId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setLayoutId($layoutId)
    {
        return $this->setData(self::LAYOUT_ID, $layoutId);
    }

    /**
     * Get identifier
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->_get(self::IDENTIFIER);
    }

    /**
     * Set identifier
     * @param string $identifier
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \BelVG\LayoutCustomizer\Api\Data\LayoutExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get height
     * @return string|null
     */
    public function getHeight()
    {
        return $this->_get(self::HEIGHT);
    }

    /**
     * Set height
     * @param string $height
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setHeight($height)
    {
        return $this->setData(self::HEIGHT, $height);
    }

    /**
     * Get width
     * @return string|null
     */
    public function getWidth()
    {
        return $this->_get(self::WIDTH);
    }

    /**
     * Set width
     * @param string $width
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setWidth($width)
    {
        return $this->setData(self::WIDTH, $width);
    }

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param string $isActive
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get horizontal_frame
     * @return int|null
     */
    public function getHorizontalFrame()
    {
        return $this->_get(self::HORIZONATAL_FRAME);
    }

    /**
     * Set horizontal_frame
     * @param int $horizontalFrame
     * @return $this
     */
    public function setHorizontalFrame($horizontalFrame)
    {
        return $this->setData(self::HORIZONATAL_FRAME, $horizontalFrame);
    }

    /**
     * Get vertical_frame
     * @return int|null
     */
    public function getVerticalFrame()
    {
        return $this->_get(self::VERTICAL_FRAME);
    }

    /**
     * Set vertical_frame
     * @param int $verticalFrame
     * @return $this
     */
    public function setVerticalFrame($verticalFrame)
    {
        return $this->setData(self::VERTICAL_FRAME, $verticalFrame);
    }

    /**
     * Get sqm_level_step2
     * @return float|null
     */
    public function getSqmLevelStep2()
    {
        return $this->_get(self::SQM_LEVEL_STEP2);
    }

    /**
     * Set sqm_level_step2
     * @param float $sqmLevelStep2
     * @return $this
     */
    public function setSqmLevelStep2($sqmLevelStep2)
    {
        return $this->setData(self::SQM_LEVEL_STEP2, $sqmLevelStep2);
    }

    // TODO: comment

    public function getBasePrice()
    {
        return $this->_get(self::BASE_PRICE);
    }

    public function setBasePrice($price)
    {
        return $this->setData(self::BASE_PRICE, $price);
    }

    public function getSqmPrice()
    {
        return $this->_get(self::SQM_PRICE);
    }

    public function setSqmPrice($price)
    {
        return $this->setData(self::SQM_PRICE, $price);
    }

    public function getSqmPriceStep2()
    {
        return $this->_get(self::SQM_PRICE_STEP2);
    }

    public function setSqmPriceStep2($price)
    {
        return $this->setData(self::SQM_PRICE_STEP2, $price);
    }

    public function getFamilyId()
    {
        return $this->_get(self::FAMILY_ID);
    }

    public function setFamilyId($family_id)
    {
        return $this->setData(self::FAMILY_ID, $family_id);
    }

    public function getLayoutmaterialId()
    {
        return $this->_get(self::LAYOUTMATERIAL_ID);
    }

    public function setLayoutmaterialId($material_id)
    {
        return $this->setData(self::LAYOUTMATERIAL_ID, $material_id);
    }



    /**
     * Get inoutcolor_price_both_diff
     * @return float|null
     */
    public function getInoutcolorPriceBothDiff()
    {
        return $this->_get(self::INOUTCOLOR_PRICE_BOTH_DIFF);
    }

    /**
     * {@inheritdoc}
     */
    public function setInoutcolorPriceBothDiff($inoutcolorPriceBothDiff)
    {
        return $this->setData(self::INOUTCOLOR_PRICE_BOTH_DIFF, $inoutcolorPriceBothDiff);
    }

    /**
     * {@inheritdoc}
     */
    public function getInoutcolorPriceBothSame()
    {
        return $this->_get(self::INOUTCOLOR_PRICE_BOTH_SAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setInoutcolorPriceBothSame($inoutcolorPriceBothSame)
    {
        return $this->setData(self::INOUTCOLOR_PRICE_BOTH_SAME, $inoutcolorPriceBothSame);
    }

    /**
     * {@inheritdoc}
     */
    public function getInoutcolorPriceInsideOtherwhite()
    {
        return $this->_get(self::INOUTCOLOR_PRICE_INSIDE_OTHERWHITE);
    }

    /**
     * {@inheritdoc}
     */
    public function setInoutcolorPriceInsideOtherwhite($inoutcolorPriceInsideOtherwhite)
    {
        return $this->setData(self::INOUTCOLOR_PRICE_INSIDE_OTHERWHITE, $inoutcolorPriceInsideOtherwhite);
    }

    /**
     * {@inheritdoc}
     */
    public function getInoutcolorPriceOutsideOtherwhite()
    {
        return $this->_get(self::INOUTCOLOR_PRICE_OUTSIDE_OTHERWHITE);
    }

    /**
     * {@inheritdoc}
     */
    public function setInoutcolorPriceOutsideOtherwhite($inoutcolorPriceOutsideOtherwhite)
    {
        return $this->setData(self::INOUTCOLOR_PRICE_OUTSIDE_OTHERWHITE, $inoutcolorPriceOutsideOtherwhite);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}
