<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Config;

use BelVG\Factory\Api\Data\FactoryMaterialDeliveryTypeInterface;

/**
 * Class FactoryMaterialDeliveryTypesPool
 *
 * @package BelVG\Factory\Model\Config
 */
class FactoryMaterialDeliveryTypesPool
{
    /**
     * @var FactoryMaterialDeliveryTypeInterface[]
     */
    protected array $factoryMaterialDeliveryTypes;

    /**
     * FactoryMaterialDeliveryTypesPool constructor.
     *
     * @param array $factoryMaterialDeliveryTypes
     */
    public function __construct(array $factoryMaterialDeliveryTypes = [])
    {
        $this->factoryMaterialDeliveryTypes = $factoryMaterialDeliveryTypes;
    }

    /**
     * @return array
     */
    public function getFactoryMaterialDeliveryTypesPool(): array
    {
        return $this->factoryMaterialDeliveryTypes;
    }
}
