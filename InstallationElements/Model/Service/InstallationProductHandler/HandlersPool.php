<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\InstallationProductHandler;


use BelVG\InstallationElements\Api\Service\InstallationProductHandler\HandlerInterface;
use Psr\Log\LoggerInterface;

class HandlersPool
{

    private const LOG_PREFIX = '[BelVG_InstallationElements::HandlersPool]: ';

    public function __construct(
        protected LoggerInterface $logger,
        protected iterable $handlers
    ) {
        $this->handlers = $this->getValidHandlers($handlers);
    }

    public function getHandlers(): iterable
    {
        return $this->handlers;
    }

    private function getValidHandlers(iterable $handlers): iterable
    {
        $valid = [];
        foreach ($handlers as $handler) {
            if ($handler instanceof HandlerInterface) {
                $valid[] = $handler;
                continue;
            }

            $this->logger->warning(sprintf(
                self::LOG_PREFIX . ' class "%s" does not implement "%s" interface',
                get_class($handler),
                HandlerInterface::class
            ));
        }
        unset($handler);

        return $valid;
    }
}