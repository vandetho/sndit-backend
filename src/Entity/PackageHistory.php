<?php

namespace App\Entity;

use App\Repository\PackageHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

/**
 * Class PackageHistory
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: PackageHistoryRepository::class)]
#[ORM\Table(name: 'sndit_package_history')]
class PackageHistory extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[Column(name: 'transition_name', type: Types::STRING, length: 100, nullable: false)]
    private ?string $transitionName;

    /**
     * @var string|null
     */
    #[Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description;

    /**
     * @var Package|null
     */
    #[ORM\ManyToOne(targetEntity: Package::class)]
    #[ORM\JoinColumn(name: 'package_id', referencedColumnName: 'id', nullable: false)]
    private ?Package $package;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user;

    /**
     * @return string|null
     */
    public function getTransitionName(): ?string
    {
        return $this->transitionName;
    }

    /**
     * @param string|null $transitionName
     */
    public function setTransitionName(?string $transitionName): void
    {
        $this->transitionName = $transitionName;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Package|null
     */
    public function getPackage(): ?Package
    {
        return $this->package;
    }

    /**
     * @param Package|null $package
     */
    public function setPackage(?Package $package): void
    {
        $this->package = $package;
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
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
