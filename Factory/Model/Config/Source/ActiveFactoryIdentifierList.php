<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Config\Source;

use BelVG\Factory\Model\Factory;
use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Model\ResourceModel\Factory\Collection as FactoryCollection;
use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory as FactoryCollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Data\OptionSourceInterface;

class ActiveFactoryIdentifierList extends DataObject implements OptionSourceInterface
{
    public function __construct(
        protected FactoryCollectionFactory $factoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($data);
    }

    /**
     * Generate list of factories
     *
     * @return string[]
     */
    public function toOptionArray(): array
    {
        /** @var FactoryCollection $factoryCollection */
        $factoryCollection = $this->factoryCollectionFactory->create();
        $factoryCollection->setStoreId(0);

        $options = [['value' => '', 'label' => __('-- Please Select --')]];
        /** @var Factory $factory */
        foreach ($factoryCollection as $factory) {
            if (!$factory[FactoryInterface::IS_ACTIVE]) {
                continue;
            }

            $options[] = [
                'value' => $factory->getData('identifier'),
                'label' => $factory->getName()
            ];
        }

        return $options;
    }
}
