<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\UserNotificationTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserNotificationToken
 *
 * @package App\Entity
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: UserNotificationTokenRepository::class)]
#[ORM\Table(name: 'sndit_user_notification_token')]
class UserNotificationToken extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'token', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $token = null;

    /**
     * @var bool
     *
     */
    #[ORM\Column(name: 'valid', type: Types::BOOLEAN, nullable: false)]
    private bool $valid = true;

    /**
     * @var string
     */
    #[ORM\Column(name: 'communication_type', type: Types::STRING, length: 20, nullable: false)]
    private string $communicationType = 'expo';

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return UserNotificationToken
     */
    public function setToken(?string $token): UserNotificationToken
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     * @return UserNotificationToken
     */
    public function setValid(bool $valid): UserNotificationToken
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommunicationType(): string
    {
        return $this->communicationType;
    }

    /**
     * @param string $communicationType
     * @return UserNotificationToken
     */
    public function setCommunicationType(string $communicationType): UserNotificationToken
    {
        $this->communicationType = $communicationType;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return UserNotificationToken
     */
    public function setUser(?User $user): UserNotificationToken
    {
        $this->user = $user;

        return $this;
    }
}
