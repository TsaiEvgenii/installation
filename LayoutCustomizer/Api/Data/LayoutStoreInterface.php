<?php


namespace BelVG\LayoutCustomizer\Api\Data;

interface LayoutStoreInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const LAYOUTSTORE_ID = 'layoutstore_id';
    const LAYOUT_ID = 'layout_id';
    const SQM_PRICE = 'sqm_price';
    const BASE_PRICE = 'base_price';
    const STORE_ID = 'store_id';
    const SQM_PRICE_STEP2 = 'sqm_price_step2';

    /**
     * Get layoutstore_id
     * @return string|null
     */
    public function getLayoutstoreId();

    /**
     * Set layoutstore_id
     * @param string $layoutstoreId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setLayoutstoreId($layoutstoreId);

    /**
     * Get layout_id
     * @return string|null
     */
    public function getLayoutId();

    /**
     * Set layout_id
     * @param string $layoutId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setLayoutId($layoutId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutStoreExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \BelVG\LayoutCustomizer\Api\Data\LayoutStoreExtensionInterface $extensionAttributes
    );

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setStoreId($storeId);

    /**
     * Get base_price
     * @return string|null
     */
    public function getBasePrice();

    /**
     * Set base_price
     * @param string $basePrice
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setBasePrice($basePrice);

    /**
     * Get sqm_price
     * @return string|null
     */
    public function getSqmPrice();

    /**
     * Set sqm_price
     * @param string $sqmPrice
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setSqmPrice($sqmPrice);

    /**
     * Get sqm_price_step2
     * @return string|null
     */
    public function getSqmPriceStep2();

    /**
     * Set sqm_price_step2
     * @param string $sqmPriceStep2
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     */
    public function setSqmPriceStep2($sqmPriceStep2);
}