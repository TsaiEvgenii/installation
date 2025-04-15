<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Config\FactoryMaterialDeliveryType;

use BelVG\Factory\Api\Data\FactoryMaterialDeliveryTypeInterface;
use Magento\Framework\Phrase;

/**
 * Class CategoryFactoryMaterialDeliveryType
 *
 * @package BelVG\Factory\Model\Config\FactoryMaterialDeliveryType
 */
class CategoryFactoryMaterialDeliveryType implements FactoryMaterialDeliveryTypeInterface
{
    public const CATEGORY_COLOUR = 'category_colour';

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return self::CATEGORY_COLOUR;
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): Phrase
    {
        return __('Category + Colour');
    }
}
