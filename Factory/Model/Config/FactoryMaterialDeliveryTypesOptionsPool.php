<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Config;

use BelVG\Factory\Api\Data\FactoryMaterialDeliveryTypeOptionsInterface;

/**
 * Class FactoryMaterialDeliveryTypesOptionsPool
 *
 * @package BelVG\Factory\Model\Config
 */
class FactoryMaterialDeliveryTypesOptionsPool
{
    /**
     * @var FactoryMaterialDeliveryTypeOptionsInterface[]
     */
    protected array $factoryMaterialDeliveryTypesOptions;

    /**
     * FactoryMaterialDeliveryTypesOptionsPool constructor.
     *
     * @param array $factoryMaterialDeliveryTypesOptions
     */
    public function __construct(array $factoryMaterialDeliveryTypesOptions = [])
    {
        $this->factoryMaterialDeliveryTypesOptions = $factoryMaterialDeliveryTypesOptions;
    }

    /**
     * @return array
     */
    public function getFactoryMaterialDeliveryTypesOptionsPool(): array
    {
        return $this->factoryMaterialDeliveryTypesOptions;
    }
}
