<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Service\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CalculationTypeOptions implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => CalculationTypes::DYNAMIC->value, 'label' => __(CalculationTypeLabels::DYNAMIC->value)],
            ['value' => CalculationTypes::STATIC->value, 'label' => __(CalculationTypeLabels::STATIC->value)]
        ];
    }
}
