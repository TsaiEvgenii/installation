<?php


namespace BelVG\LayoutCustomizer\Observer;


use Magento\Framework\Event\Observer;
use BelVG\LayoutCustomizer\Helper\Data as LayoutHelper;

class LayoutDeleteAfter implements \Magento\Framework\Event\ObserverInterface
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

    public function nullProductLayoutAttribute(array $prod_ids, $store_id)
    {
        $this->productActionObject->updateAttributes($prod_ids, [LayoutHelper::PRODUCT_LAYOUT_ATTR => null], $store_id);
    }

    public function execute(Observer $observer)
    {
        /** @var \Belvg\LayoutCustomizer\Model\Layout $layout */
        $layout = $observer->getEvent()->getData('data_object');
        $store_id = (int) $this->request->getParam('store', 0);

        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(LayoutHelper::PRODUCT_LAYOUT_ATTR, $layout->getData('layout_id'));

        $prod_ids = [];
        foreach ($productCollection as $product) {
            $prod_ids[] = $product->getId();
        }

        $this->nullProductLayoutAttribute($prod_ids, $store_id);
    }
}
