<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Api\FactoryRepositoryMaterial\AfterSave;

use BelVG\Factory\Api\Data\FactoryInterface;

interface ActionInterface
{
    public function execute(FactoryInterface $factory, int $storeId = 0);
}
