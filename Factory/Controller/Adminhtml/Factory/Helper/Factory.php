<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory\Helper;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\Data\FactoryInterfaceFactory as ObjectFactory;
use BelVG\Factory\Model\FactoryRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Registry;
use Magento\Framework\Api\DataObjectHelper;

class Factory
{
    const PERSISTOR_NAME = 'belvg_factory';

    protected $registry;
    protected $storeManager;
    protected $factoryRepo;
    protected $objectFactory;
    protected $dataPersistor;
    protected $dataObjectHelper;

    public function __construct(
        Registry $registry,
        StoreManagerInterface $storeManager,
        ObjectFactory $objectFactory,
        FactoryRepository $factoryRepo,
        DataPersistorInterface $dataPersistor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->objectFactory = $objectFactory;
        $this->factoryRepo = $factoryRepo;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    public function initStore(Request $request)
    {
        $storeId = (int) $request->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store);
        return $store;
    }

    public function initObject(Request $request, $required = false)
    {
        $id = $request->getParam('factory_id');

        if ($required && empty($id))
            throw new Exception\LocalizedException(__('No factory ID provided'));

        $object = !empty($id)
            ? $this->factoryRepo->getById($id, $this->getStore()->getId())
            : $this->objectFactory->create();

        // Register
        $this->registry->register('belvg_factory', $object);

        return $object;
    }

    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    public function saveObject(FactoryInterface $factory, array $data)
    {
        $this->dataObjectHelper->populateWithArray($factory, $data, FactoryInterface::class);

        return $this->factoryRepo->save($factory, $this->getStore()->getId());
    }

    public function deleteObject(FactoryInterface $factory)
    {
        $this->factoryRepo->delete($factory);
    }

    public function storeFormData(array $data)
    {
        $this->dataPersistor->set(self::PERSISTOR_NAME, $data);
    }

    public function clearFormData()
    {
        $this->dataPersistor->clear(self::PERSISTOR_NAME);
    }
}
