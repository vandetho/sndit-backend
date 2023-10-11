<?php
declare(strict_types=1);


namespace App\Entity;

use App\Repository\InternalLastLoginRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class InternalLastLogin
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: InternalLastLoginRepository::class)]
#[ORM\Table(name: 'sndit_internal_last_login')]
class InternalLastLogin extends AbstractEntity
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
     * @var InternalUser|null
     */
    #[ORM\ManyToOne(targetEntity: InternalUser::class)]
    #[ORM\JoinColumn(name: "internal_user_id", referencedColumnName: "id", nullable: false)]
    private ?InternalUser $user = null;

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     * @return InternalLastLogin
     */
    public function setIp(?string $ip): InternalLastLogin
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
     * @return InternalLastLogin
     */
    public function setDevice(?string $device): InternalLastLogin
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @return InternalUser|null
     */
    public function getInternalUser(): ?InternalUser
    {
        return $this->user;
    }

    /**
     * @param InternalUser|null $user
     * @return InternalLastLogin
     */
    public function setInternalUser(?InternalUser $user): InternalLastLogin
    {
        $this->user = $user;

        return $this;
    }
}
