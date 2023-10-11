<?php
declare(strict_types=1);



namespace App\Event\User;


use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Class CheckUserByIdentifierEvent
 *
 * @package App\Event\User
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckUserByIdentifierEvent extends AbstractEvent
{
    /**
     * @var string
     */
    private string $identifier;

    /**
     * @var User
     */
    private User $user;

    /**
     * CheckUserByIdentifierEvent constructor.
     *
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
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
     * @return CheckUserByIdentifierEvent
     */
    public function setUser(User $user): CheckUserByIdentifierEvent
    {
        $this->user = $user;

        return $this;
    }
}
