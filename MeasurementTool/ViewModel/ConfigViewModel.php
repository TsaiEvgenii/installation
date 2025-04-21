<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\ViewModel;


use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

class ConfigViewModel implements ArgumentInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::ConfigViewModel]: ';

    public function __construct(
        private readonly FormKey $formKey,
        private readonly LoggerInterface $logger,
    ) {
    }


    public function getFormKey(): string
    {
        try {
            return $this->formKey->getFormKey();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return '';

    }
}