<?php
declare(strict_types=1);


namespace App\Workflow\Transition;


/**
 * Class StoreEmployeeTransition
 *
 * @package App\Workflow\Transition
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class EmployeeTransition
{
    public const CREATE_NEW_EMPLOYEE = 'create_new_employee';
    public const CHANGE_ROLE = 'change_role';
    public const ROLE_CHANGED = 'role_changed';
    public const REACTIVATE = 'reactivate';
    public const DEACTIVATE = 'deactivate';
}
