<?php


namespace BelVG\LayoutCustomizer\Observer;


class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    public $request;

    public $layoutModel;

    public $productActionObject;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \BelVG\LayoutCustomizer\Model\Layout $layoutModel,
        \Magento\Catalog\Model\Product\Action $productActionObject
    ) {
        $this->request = $request;
        $this->layoutModel = $layoutModel;
        $this->productActionObject = $productActionObject;
    }

    /**
     * Update Price
     *
     * @param $product
     * @param $store_id
     */
    public function updateProductPriceForStore($product, $store_id)
    {
        $layout_data = $this->layoutModel->getLayoutData($product->getData(\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR), $store_id);

        if (isset($layout_data['total_price'])) {
            $this->productActionObject->updateAttributes([$product->getId()], ['price' => $layout_data['total_price']], $store_id);
        }
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();

        $store_id = (int) $this->request->getParam('store', 0);

        if ($store_id) {
            $this->updateProductPriceForStore($product, $store_id);
        } else {
            foreach ($product->getStoreIds() as $store_id) {
                $this->updateProductPriceForStore($product, $store_id);
            }
            unset($store_id);
        }
    }

}
