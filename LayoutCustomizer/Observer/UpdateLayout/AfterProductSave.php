<?php


namespace BelVG\LayoutCustomizer\Observer\UpdateLayout;


use Psr\Log\LoggerInterface;

class AfterProductSave implements \Magento\Framework\Event\ObserverInterface
{
    private const LOG_PREFIX = '[BelVG_LayoutCustomizer::AfterProductSaveObserver]: ';

    /**
     * @var \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface
     */
    private $connectBySkuService;

    /**
     * AfterProductSave constructor.
     * @param \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface $connectBySkuService
     */
    public function __construct(
        \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface $connectBySkuService,
        protected LoggerInterface $logger
    ) {
        $this->connectBySkuService = $connectBySkuService;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     */
    private function updateLayout(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        try {
            $currentIdentifier = $product->getData(\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR);
            $productSku = $product->getSku();
            $identifier = $this->connectBySkuService->getLayoutByIdentifier($productSku);

            if ($identifier && $currentIdentifier !== $identifier->getLayoutId()) {
                foreach ($this->connectBySkuService->assign($product->getSku()) as $result) {
                    //@todo: log results???
                }
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $observer->getProduct();

        $this->updateLayout($product);
    }
}
