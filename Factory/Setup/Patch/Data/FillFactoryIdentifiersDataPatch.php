<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Setup\Patch\Data;

use BelVG\Factory\Api\FactoryRepositoryInterface;
use BelVG\Factory\Model\Factory;
use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory as FactoryCollectionFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Psr\Log\LoggerInterface;

class FillFactoryIdentifiersDataPatch implements DataPatchInterface
{
    private const LOG_PREFIX = '[BelVG_Factory::FillFactoryIdentifiersDataPatch]: ';

    public function __construct(
        private readonly FactoryRepositoryInterface $factoryRepository,
        private readonly FactoryCollectionFactory $factoryCollectionFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        try {
            $factoryCollection = $this->factoryCollectionFactory->create();
            /** @var Factory $factory */
            foreach ($factoryCollection as $factory) {
                $identifier = preg_replace('#[^0-9a-z]+#i', '-', $factory->getData(FactoryInterface::NAME));
                $identifier = strtolower($identifier);
                $factory->setData(FactoryInterface::IDENTIFIER, $identifier);

                $this->factoryRepository->save($factory->getDataModel());
            }
            unset($factory);
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $this;
    }
}
