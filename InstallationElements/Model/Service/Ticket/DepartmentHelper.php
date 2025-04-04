<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\Ticket;


use Magento\Framework\App\ResourceConnection;

class DepartmentHelper
{
    public function __construct(
        private readonly ResourceConnection $resource,
    ) {
    }

    public function getDepartmentName() :string {
        return 'Installment';
    }

    public function getDepartmentId() :int {
        $connectionM2 = $this->resource->getConnection();
        $select = $connectionM2->select()
            ->from($connectionM2->getTableName('aw_helpdesk_department'), ['id'])
            ->where('name = :name');

        return (int)$connectionM2->fetchOne($select, [':name' => $this->getDepartmentName()]);
    }

}