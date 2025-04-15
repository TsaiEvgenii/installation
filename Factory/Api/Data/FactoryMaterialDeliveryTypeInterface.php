<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Api\Data;

use Magento\Framework\Phrase;

/**
 * Interface FactoryMaterialDeliveryTypeInterface
 *
 * @package BelVG\Factory\Api\Data
 */
interface FactoryMaterialDeliveryTypeInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return Phrase
     */
    public function getLabel(): Phrase;
}
