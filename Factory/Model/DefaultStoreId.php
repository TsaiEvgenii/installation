<?php
namespace BelVG\Factory\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

trait DefaultStoreId
{
    private $storeId;

    public function getStoreId()
    {
        return !is_null($this->storeId)
            ? $this->storeId
            : $this->getStoreManager()->getStore()->getId();
    }

    public function setStoreId($store)
    {
        $this->storeId = $this->getStoreManager()->getStore($store)->getId();
        return $this;
    }

    private function getStoreManager()
    {
        return ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }
}
