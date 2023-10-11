<?php

namespace App\Command;

use App\Utils\UserManipulator;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractUserCommand
 *
 * @package App\Command
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
abstract class AbstractUserCommand extends Command
{
    /**
     * @var UserManipulator
     */
    protected UserManipulator $userManipulator;

    /**
     * ActivateUserCommand constructor.
     *
     * @param UserManipulator $userManipulator
     */
    public function __construct(UserManipulator $userManipulator)
    {
        parent::__construct();

        $this->userManipulator = $userManipulator;
    }
}
