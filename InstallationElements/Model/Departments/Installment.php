<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Departments;


use BelVG\HelpdeskOrderPageTicketCreate\Model\AbstractDepartment;

class Installment extends AbstractDepartment
{
    /**
     * @param string $type
     * @param string $title
     */
    public function __construct(
        string $type = 'installment',
        string $title = 'Installment'
    ) {
        $this->type = $type;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getGridType()
    {
        return 'installment';
    }

    /**
     * @return false
     */
    public function agentsAvailable()
    {
        return false;
    }

    /**
     * @return false
     */
    public function departmentsAvailable()
    {
        return false;
    }

    /**
     * @return false
     */
    public function isSubDepartment()
    {
        return false;
    }


    /**
     * @return false
     */
    public function isFormAvailable()
    {
        return false;
    }
}