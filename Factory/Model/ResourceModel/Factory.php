<?php
namespace BelVG\Factory\Model\ResourceModel;

use BelVG\Factory\Helper\Factory\StoreData as StoreDataHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;

class Factory extends AbstractDb
{
    protected $storeDataHelper;

    public function __construct(
        StoreDataHelper $storeDataHelper,
        DbContext $context,
        $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->storeDataHelper = $storeDataHelper;
    }

    protected function _construct()
    {
        $this->_init('belvg_factory', 'factory_id');
    }

    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        $this->loadStoreData($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->saveStoreData($object);
    }

    protected function loadStoreData(AbstractModel $object)
    {
        $this->storeDataHelper->load($object);
    }

    protected function saveStoreData(AbstractModel $object)
    {
        $this->storeDataHelper->save($object);
    }
}
