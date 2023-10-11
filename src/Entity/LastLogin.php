<?php
declare(strict_types=1);


namespace App\Entity;

use App\Repository\LastLoginRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class LastLogin
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: LastLoginRepository::class)]
#[ORM\Table(name: 'sndit_last_login')]
class LastLogin extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'ip', type: Types::STRING, length: 50, nullable: false)]
    private ?string $ip = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'device', type: Types::STRING, length: 150, nullable: true)]
    private ?string $device = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     * @return LastLogin
     */
    public function setIp(?string $ip): LastLogin
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDevice(): ?string
    {
        return $this->device;
    }

    /**
     * @param string|null $device
     * @return LastLogin
     */
    public function setDevice(?string $device): LastLogin
    {
        $this->device = $device;

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
     * @return LastLogin
     */
    public function setUser(?User $user): LastLogin
    {
        $this->user = $user;

        return $this;
    }
}
