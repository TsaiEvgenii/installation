<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Plugin\FactoryRepositoryMaterial\AfterSave;

use BelVG\Factory\Api\FactoryRepositoryMaterial\AfterSave\ActionInterface;
use Psr\Log\LoggerInterface;

class ActionsPool
{
    const LOG_PREFIX = '[BelVG_Factory::FactoryRepositoryMaterial\AfterSave\ActionsPool]: ';

    /** @var array */
    protected $actions;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        LoggerInterface $logger,
        array $actions = []
    ) {
        $this->logger = $logger;

        $this->actions = $this->getValidActions($actions);
    }

    public function getActions() :iterable {
        return $this->actions;
    }

    private function getValidActions(array $actions) :array {
        $valid = [];
        foreach ($actions as $action) {
            if ($action instanceof ActionInterface) {
                $valid[] = $action;
            } else {
                $this->logger->warning(sprintf(
                    self::LOG_PREFIX . ' class "%s" does not implement "ActionInterface" interface',
                    get_class($valid)
                ));
            }
        }
        unset($action);

        return $valid;
    }
}
