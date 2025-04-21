<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Ui\Component\Form\ElementType;


use BelVG\MeasurementTool\Model\Service\Config;
use Magento\Framework\Data\OptionSourceInterface;
use Psr\Log\LoggerInterface;

class Options implements OptionSourceInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::ElementTypeOptions]: ';

    public function __construct(
        private readonly Config $config,
        protected LoggerInterface $logger
    ) {

    }

    public function toOptionArray(): array
    {
        $options = [];
        try {
            $elementTypesData = $this->config->getElementTypes();
            foreach ($elementTypesData as $elementTypesDatum){
                $options[] = [
                    'value' => $elementTypesDatum['element_type_code'],
                    'label' => $elementTypesDatum['element_type_label']

                ];
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $options;
    }
}