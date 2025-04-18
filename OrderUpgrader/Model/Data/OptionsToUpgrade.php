<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\OrderUpgrader\Model\Data;


use BelVG\OrderUpgrader\Api\Data\OptionsToUpgradeInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class OptionsToUpgrade extends AbstractSimpleObject implements OptionsToUpgradeInterface
{
    public function getOptions(): ?array
    {
        return $this->_get(self::OPTIONS);
    }

    public function setOptions(?array $options): void
    {
        $this->setData(self::OPTIONS, $options);
    }

    /**
     * @return \BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface[]|null
     */
    public function getMaterialsMap(): ?array
    {
        return $this->_get(self::MATERIALS_MAP);
    }

    /**
     * @param \BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface[]|null $materialsMap
     *
     * @return void
     */
    public function setMaterialsMap(?array $materialsMap): void
    {
        $this->setData(self::MATERIALS_MAP, $materialsMap);
    }

    /**
     * @return \BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface[]|null
     */
    public function getPriceMap(): ?array
    {
        return $this->_get(self::PRICE_MAP);
    }

    /**
     * @param \BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface[]|null $priceMap
     *
     * @return void
     */
    public function setPriceMap(?array $priceMap): void
    {
        $this->setData(self::PRICE_MAP, $priceMap);
    }
}