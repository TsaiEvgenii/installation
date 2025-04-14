<?php


namespace BelVG\LayoutCustomizer\Observer;


use Magento\Framework\Event\Observer;

class LayoutSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    public $productCollectionFactory;
    public $productActionObject;
    public $request;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Action $productActionObject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productActionObject = $productActionObject;
        $this->request = $request;
    }


    public function updateProductPriceForStore($layout, $product, $store_id)
    {
        $layout_data = $layout->getLayoutData($product->getData(\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR), $store_id);

        if (isset($layout_data['total_price'])) {
            $this->productActionObject->updateAttributes([$product->getId()], ['price' => $layout_data['total_price']], $store_id);
        }
    }

    public function execute(Observer $observer)
    {
        /** @var \Belvg\LayoutCustomizer\Model\Layout $layout */
        $layout = $observer->getEvent()->getData('data_object');

        $store_id = (int) $this->request->getParam('store', 0);
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR, $layout->getData('layout_id'));
        foreach ($productCollection as $product) {
            if ($store_id) {
                $this->updateProductPriceForStore($layout, $product, $store_id);
            } else {
                foreach ($product->getStoreIds() as $productStoreId) {
                    $this->updateProductPriceForStore($layout, $product, $productStoreId);
                }
            }
        }
    }
}
