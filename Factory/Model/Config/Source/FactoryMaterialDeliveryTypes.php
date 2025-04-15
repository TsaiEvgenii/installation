<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use BelVG\Factory\Model\Config\FactoryMaterialDeliveryTypesPool;

/**
 * Class FactoryMaterialDeliveryTypes
 *
 * @package BelVG\Factory\Model\Config\Source
 */
class FactoryMaterialDeliveryTypes implements OptionSourceInterface
{
    /**
     * @var FactoryMaterialDeliveryTypesPool
     */
    protected FactoryMaterialDeliveryTypesPool $factoryMaterialDeliveryTypesPool;

    /**
     * FactoryMaterialDeliveryTypes constructor.
     *
     * @param FactoryMaterialDeliveryTypesPool $factoryMaterialDeliveryTypesPool
     */
    public function __construct(FactoryMaterialDeliveryTypesPool $factoryMaterialDeliveryTypesPool)
    {
        $this->factoryMaterialDeliveryTypesPool = $factoryMaterialDeliveryTypesPool;
    }

    /**
     * @param bool $withDefault
     * @return array
     */
    public function toOptionArray(bool $withDefault = false): array
    {
        $options = $this->factoryMaterialDeliveryTypesPool->getFactoryMaterialDeliveryTypesPool();
        $result = [];
        if ($withDefault) {
            $result[] = ['value' => '', 'label' => __('No selected')];
        }
        foreach ($options as $option) {
            $result[] = ['value' => $option->getType(), 'label' => $option->getLabel()];
        }
        return $result;
    }
}
