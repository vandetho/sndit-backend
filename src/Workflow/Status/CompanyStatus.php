<?php
declare(strict_types=1);


namespace App\Workflow\Status;


/**
 * Class CompanyStatus
 *
 * @package App\Workflow\Status
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class CompanyStatus
{
    public const NEW_COMPANY = 'new_company';
    public const LISTING = 'listing';
    public const ADDING_EMPLOYEE = 'adding_employee';
    public const UPDATING_INFORMATION = 'updating_information';

}
