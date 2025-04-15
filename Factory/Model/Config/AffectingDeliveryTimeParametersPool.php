<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Config;

use BelVG\Factory\Api\Data\AffectingDeliveryTimeParametersInterface;

/**
 * Class AffectingDeliveryTimeParametersPool
 *
 * @package BelVG\Factory\Model\Config
 */
class AffectingDeliveryTimeParametersPool
{
    /**
     * @var AffectingDeliveryTimeParametersInterface[]
     */
    protected array $affectingDeliveryTimeParameters;

    /**
     * AffectingDeliveryTimeParametersPool constructor.
     *
     * @param array $affectingDeliveryTimeParameters
     */
    public function __construct(array $affectingDeliveryTimeParameters = [])
    {
        $this->affectingDeliveryTimeParameters = $affectingDeliveryTimeParameters;
    }

    /**
     * @return array
     */
    public function getAffectingDeliveryTimeParametersPool(): array
    {
        return $this->affectingDeliveryTimeParameters;
    }
}
