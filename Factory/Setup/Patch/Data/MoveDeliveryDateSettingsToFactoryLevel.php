<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\FactoryRepositoryInterface;
use BelVG\Factory\Model\Factory;
use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory;

class MoveDeliveryDateSettingsToFactoryLevel implements DataPatchInterface
{
    private const XML_PATH_PREFIX = 'belvg_orderfactory/delivery/';

    /**
     * @param CollectionFactory $collectionFactory
     * @param FactoryRepositoryInterface $factoryRepository
     * @param WriterInterface $writer
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private CollectionFactory $collectionFactory,
        private FactoryRepositoryInterface $factoryRepository,
        private WriterInterface $writer,
        private StoreManagerInterface $storeManager
    ) {

    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        /**
         * To define a dependency in a patch, add the method public static function getDependencies()
         * to the patch class and return the class names of the patches this patch depends on.
         * The dependency can be in any module.
         */
        return [];
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }

    /**
     * @return \BelVG\AmastyOrderStatus\Setup\Patch\Data\FixAmastyOrderStatusTableAliases
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function apply(): MoveDeliveryDateSettingsToFactoryLevel
    {
        $this->removeConfig();
        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {

            $this->removeConfig(ScopeInterface::SCOPE_STORES, (int) $store->getId());
            $this->removeConfig(ScopeInterface::SCOPE_WEBSITES, (int) $store->getWebsiteId());

            $factoryCollection = $this->collectionFactory->create()->setStoreId($store->getId());
            foreach ($factoryCollection as $factory) {

                $this->saveForFactory($factory, (int) $store->getId());
            }
        }

        return $this;
    }

    /**
     * @param string $scope
     * @param int $scopeId
     * @return void
     */
    private function removeConfig(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, int $scopeId = 0): void
    {
        foreach ($this->getMovedSettingNames() as $movedSettingName) {
            $this->writer->delete(self::XML_PATH_PREFIX . $movedSettingName, $scope, $scopeId);
        }
    }

    /**
     * @param Factory $factory
     * @param int $storeId
     * @return void
     */
    private function saveForFactory(Factory $factory, int $storeId): void
    {
        if (isset($this->getConfigValues()[$storeId])) {
            foreach ($this->getConfigValues()[$storeId] as $movedSettingName => $movedSettingValue) {

                $factory->setData($movedSettingName, $movedSettingValue);
            }

            $this->factoryRepository->save($factory->getDataModel(), $storeId);
        }
    }

    /**
     * @return string[]
     */
    private function getMovedSettingNames(): array
    {
        return [
            FactoryInterface::DELIVERY_WEEKS_INTERVAL,
            FactoryInterface::DELIVERY_WEEKS_INTERVAL_FRONTEND,
            FactoryInterface::FACTORY_DELIVERY_TRANSPORT_TIME,
            FactoryInterface::FACTORY_DELIVERY_WEEKS_INTERVAL,
        ];
    }

    /**
     * @return string[][]
     */
    private function getConfigValues(): array
    {
        $keys = $this->getMovedSettingNames();
        return [
            0 => array_combine($keys, [1, 1, 1, 0]),
            1 => array_combine($keys, [2, 2, 2, 0]),
            5 => array_combine($keys, [2, 2, 5, 0]),
            6 => array_combine($keys, [2, 2, 3, 0]),
            7 => array_combine($keys, [3, 3, 3, 0]),
            8 => array_combine($keys, [2, 2, 3, 0]),
        ];
    }
}
