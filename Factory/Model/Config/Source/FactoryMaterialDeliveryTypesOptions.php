<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use BelVG\Factory\Model\Config\FactoryMaterialDeliveryTypesOptionsPool;

/**
 * Class FactoryMaterialDeliveryTypesOptions
 *
 * @package BelVG\Factory\Model\Config\Source
 */
class FactoryMaterialDeliveryTypesOptions implements OptionSourceInterface
{
    /**
     * @var FactoryMaterialDeliveryTypesOptionsPool
     */
    protected FactoryMaterialDeliveryTypesOptionsPool $factoryMaterialDeliveryTypesOptionsPool;

    /**
     * FactoryMaterialDeliveryTypesOptions constructor.
     *
     * @param FactoryMaterialDeliveryTypesOptionsPool $factoryMaterialDeliveryTypesOptionsPool
     */
    public function __construct(FactoryMaterialDeliveryTypesOptionsPool $factoryMaterialDeliveryTypesOptionsPool)
    {
        $this->factoryMaterialDeliveryTypesOptionsPool = $factoryMaterialDeliveryTypesOptionsPool;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $factoryMaterialDeliveryTypesOptions =
            $this->factoryMaterialDeliveryTypesOptionsPool->getFactoryMaterialDeliveryTypesOptionsPool();
        $result = [];
        foreach ($factoryMaterialDeliveryTypesOptions as $factoryMaterialDeliveryTypeOptions) {
            $factoryMaterialDeliveryTypeOptions = $factoryMaterialDeliveryTypeOptions->toOptionArray();
            $result = array_merge($result, $factoryMaterialDeliveryTypeOptions);
        }
        unset($factoryMaterialDeliveryTypeOptions);
        return $result;
    }
}
