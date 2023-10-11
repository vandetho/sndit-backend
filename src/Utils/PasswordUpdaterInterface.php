<?php

namespace App\Utils;

use App\Entity\User;

/**
 * Interface PasswordUpdaterInterface
 *
 * @package App\Utils
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
interface PasswordUpdaterInterface
{
    /**
     * Updates the hashed password in the user when there is a new password.
     *
     * The implement should be a no-op in case there is no new password (it should not erase the
     * existing hash with a wrong one).
     *
     * @param User $user
     * @return void
     */
    public function hashPassword(User $user): void;
}
