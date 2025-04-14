<?php


namespace BelVG\LayoutCustomizer\Observer\UpdateLayout;


class AfterLayoutSave implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface
     */
    private $connectBySkuService;

    /**
     * AfterLayoutSave constructor.
     * @param \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface $connectBySkuService
     */
    public function __construct(
        \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface $connectBySkuService
    ) {
        $this->connectBySkuService = $connectBySkuService;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Belvg\LayoutCustomizer\Model\Layout $layout */
        $layout = $observer->getEvent()->getData('data_object');

        $old_identifier = $layout->getOrigData('identifier');
        $identifier = $layout->getData('identifier');
        //update products only if identifier was updated
        if ($old_identifier != $identifier) {
            $this->connectBySkuService->unassign($old_identifier);
            foreach ($this->connectBySkuService->assign($identifier) as $result) {
                //@todo: log results???
            }
        }
    }
}
