<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model\Log;


class InfoLogger extends \Monolog\Logger
{
    public function __construct(
        $name,
        $handlers = array(),
        array $processors = array()
    ) {
        parent::__construct($name, $handlers, $processors);
    }

    public function addProcessorInfoLog($storeId,$groupId,$productIds)
    {
        parent::info('store id: ' . $storeId . ' group id: ' . $groupId . ' products: ['  . implode(',', $productIds) . ']', []);
    }

    public function addInfoLog($message, $storeId = null, $groupId = null)
    {
        parent::info('store id: ' . $storeId . ' group id: ' . $groupId . ' message: ' . $message, []);
    }
}

