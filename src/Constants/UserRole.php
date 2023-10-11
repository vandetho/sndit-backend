<?php
declare(strict_types=1);


namespace App\Constants;


/**
 * Class UserRole
 *
 * @package App\Constants
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class UserRole extends AbstractConstant
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
