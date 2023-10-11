<?php
declare(strict_types=1);


namespace App\Event\Employee;


use App\Entity\Employee;
use App\Event\AbstractEvent;

/**
 * Class CheckEmployeeExistEvent
 *
 * @package App\Event\Employee
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckEmployeeExistEvent extends AbstractEvent
{
    /**
     * @var int|string
     */
    private int|string $idOrToken;

    /**
     * @var Employee
     */
    private Employee $employee;

    /**
     * CheckEmployeeExistEvent constructor.
     *
     * @param int|string $idOrToken
     */
    public function __construct(int|string $idOrToken)
    {
        $this->idOrToken = $idOrToken;
    }

    /**
     * @return int|string
     */
    public function getIdOrToken(): int|string
    {
        return $this->idOrToken;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     * @return CheckEmployeeExistEvent
     */
    public function setEmployee(Employee $employee): CheckEmployeeExistEvent
    {
        $this->employee = $employee;

        return $this;
    }
}
