<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Service\Source;

use Magento\Framework\Data\OptionSourceInterface;
use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory;

class Factories implements OptionSourceInterface
{
    /**
     * [factory_id => factory]
     *
     * @var array
     */
    private array $allFactories = [];

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {

    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        $factories = $this->getAllFactories();
        $result = [];
        foreach ($factories as $factory) {

            $result[] = [
                'value' => $factory->getFactoryId(),
                'label' => $factory->getName(),
            ];
        }

        return $result;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getAllFactories(?int $storeId = null): array
    {
        $this->allFactories["$storeId"] =
            !empty($this->allFactories["$storeId"]) ? $this->allFactories["$storeId"] : $this->findAllFactories($storeId);

        return $this->allFactories["$storeId"];
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    private function findAllFactories(?int $storeId): array
    {
        $factoriesById = [];
        $factories = $this->collectionFactory->create();
        if (!is_null($storeId)) {

            $factories->setStoreId($storeId);
        }
        foreach ($factories as $factory) {

            $factoriesById[$factory->getFactoryId()] = $factory;
        }
        return $factoriesById;
    }
}
