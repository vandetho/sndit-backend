<?php
declare(strict_types=1);


namespace App\Workflow\Status;


/**
 * Class EmployeeStatus
 *
 * @package App\Workflow\Status
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class EmployeeStatus
{
    public const NEW_EMPLOYEE = 'new_employee';
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const CHANGING_ROLE = 'changing_role';

}
