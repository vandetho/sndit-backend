<?php
declare(strict_types=1);

namespace App\Event\User;


use App\Entity\User;
use App\Event\AbstractEvent;


/**
 * Class CheckUserExistEvent
 *
 * @package App\Event\User
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckUserExistEvent extends AbstractEvent
{
    /**
     * @var int|string
     */
    private string|int $idOrToken;

    /**
     * @var User
     */
    private User $user;

    /**
     * CheckUserExistEvent constructor.
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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return CheckUserExistEvent
     */
    public function setUser(User $user): CheckUserExistEvent
    {
        $this->user = $user;

        return $this;
    }
}
