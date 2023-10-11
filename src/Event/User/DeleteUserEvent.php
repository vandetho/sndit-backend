<?php

namespace App\Event\User;

use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Class DeleteUserEvent
 *
 * @package App\Event\User
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class DeleteUserEvent extends AbstractEvent
{
    /**
     * @var User
     */
    private User $user;

    /**
     * DeleteUserEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
