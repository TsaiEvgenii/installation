<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Model\Service;

use BelVG\Factory\Api\Data\FactoryInterface;

class FactoryMailerService
{
    public function getFactoryEmails(FactoryInterface $factory) :iterable {
        return array_map('trim', explode(',', $factory->getEmail()));
    }
}
