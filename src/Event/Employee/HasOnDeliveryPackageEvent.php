<?php

namespace App\Event\Employee;

use App\Entity\Employee;
use App\Event\AbstractEvent;

/**
 * Class HasOnDeliveryPackageEvent
 *
 * @package App\Event\Employee
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class HasOnDeliveryPackageEvent extends AbstractEvent
{
    /**
     * @var Employee
     */
    private Employee $employee;

    /**
     * HasOnDeliveryPackageEvent constructor.
     *
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }
}
