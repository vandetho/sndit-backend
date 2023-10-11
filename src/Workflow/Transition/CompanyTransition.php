<?php
declare(strict_types=1);


namespace App\Workflow\Transition;


/**
 * Class CompanyTransition
 *
 * @package App\Workflow\Transition
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class CompanyTransition
{
    public const CREATE_COMPANY = 'create_company';
    public const ADD_EMPLOYEE = 'add_employee';
    public const EMPLOYEE_ADDED = 'employee_added';
    public const UPDATE_INFORMATION = 'update_information';
    public const INFORMATION_UPDATED = 'information_updated';
}
