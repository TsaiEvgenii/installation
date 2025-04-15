<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Config\Source;

use BelVG\Factory\Model\Factory;
use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory as FactoryCollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Data\OptionSourceInterface;

class FactoryIdentifierList extends DataObject implements OptionSourceInterface
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
        $factoryCollection = $this->factoryCollectionFactory->create();
        $options = [['value' => '', 'label' => __('-- Please Select --')]];
        /** @var Factory $factory */
        foreach ($factoryCollection as $factory) {
            $options[] = [
                'value' => $factory->getData('identifier'),
                'label' => $factory->getName()
            ];
        }

        return $options;
    }
}
