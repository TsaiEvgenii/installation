<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use BelVG\Factory\Model\Config\AffectingDeliveryTimeParametersPool;

/**
 * Class AffectingDeliveryTimeParameters
 *
 * @package BelVG\Factory\Model\Config\Source
 */
class AffectingDeliveryTimeParameters implements OptionSourceInterface
{
    /**
     * @var AffectingDeliveryTimeParametersPool
     */
    protected AffectingDeliveryTimeParametersPool $affectingDeliveryTimeParametersPool;

    /**
     * AffectingDeliveryTimeParameters constructor.
     *
     * @param AffectingDeliveryTimeParametersPool $affectingDeliveryTimeParametersPool
     */
    public function __construct(AffectingDeliveryTimeParametersPool $affectingDeliveryTimeParametersPool)
    {
        $this->affectingDeliveryTimeParametersPool = $affectingDeliveryTimeParametersPool;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $affectingDeliveryTimeParametersPool =
            $this->affectingDeliveryTimeParametersPool->getAffectingDeliveryTimeParametersPool();
        $result = [];
        foreach ($affectingDeliveryTimeParametersPool as $affectingDeliveryTimeParameters) {
            $affectingDeliveryTimeParameters = $affectingDeliveryTimeParameters->toOptionArray();
            $result = array_merge($result, $affectingDeliveryTimeParameters);
        }
        unset($affectingDeliveryTimeParameters);
        return $result;
    }
}
