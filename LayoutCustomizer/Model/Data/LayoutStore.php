<?php


namespace BelVG\LayoutCustomizer\Model\Data;

use BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface;

class LayoutStore extends \Magento\Framework\Api\AbstractExtensibleObject implements LayoutStoreInterface
{

    /**
     * Get layoutstore_id
     * @return string|null
     */
    public function getLayoutstoreId()
    {
        return $this->_get(self::LAYOUTSTORE_ID);
    }

    /**
     * Set layoutstore_id
     * @param string $layoutstoreId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setLayoutstoreId($layoutstoreId)
    {
        return $this->setData(self::LAYOUTSTORE_ID, $layoutstoreId);
    }

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
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setLayoutId($layoutId)
    {
        return $this->setData(self::LAYOUT_ID, $layoutId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutStoreExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \BelVG\LayoutCustomizer\Api\Data\LayoutStoreExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $storeId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get base_price
     * @return string|null
     */
    public function getBasePrice()
    {
        return $this->_get(self::BASE_PRICE);
    }

    /**
     * Set base_price
     * @param string $basePrice
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setBasePrice($basePrice)
    {
        return $this->setData(self::BASE_PRICE, $basePrice);
    }

    /**
     * Get sqm_price
     * @return string|null
     */
    public function getSqmPrice()
    {
        return $this->_get(self::SQM_PRICE);
    }

    /**
     * Set sqm_price
     * @param string $sqmPrice
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setSqmPrice($sqmPrice)
    {
        return $this->setData(self::SQM_PRICE, $sqmPrice);
    }

    /**
     * Get sqm_price_step2
     * @return string|null
     */
    public function getSqmPriceStep2()
    {
        return $this->_get(self::SQM_PRICE_STEP2);
    }

    /**
     * Set sqm_price_step2
     * @param string $sqmPriceStep2
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setSqmPriceStep2($sqmPriceStep2)
    {
        return $this->setData(self::SQM_PRICE_STEP2, $sqmPriceStep2);
    }
}