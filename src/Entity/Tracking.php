<?php

namespace App\Entity;

use App\Repository\TrackingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tracking
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Table(name: 'sndit_tracking')]
#[ORM\Entity(repositoryClass: TrackingRepository::class)]
class Tracking extends AbstractEntity
{
    /**
     * @var float|null
     */
    #[ORM\Column(name: 'latitude', type: Types::DECIMAL, precision: 11, scale: 8, nullable: true)]
    private ?float $latitude = null;

    /**
     * @var float|null
     */
    #[ORM\Column(name: 'longitude', type: Types::DECIMAL, precision: 11, scale: 8, nullable: true)]
    private ?float $longitude = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|null $latitude
     * @return Tracking
     */
    public function setLatitude(?float $latitude): Tracking
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|null $longitude
     * @return Tracking
     */
    public function setLongitude(?float $longitude): Tracking
    {
        $this->longitude = $longitude;

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
     * @return Tracking
     */
    public function setUser(?User $user): Tracking
    {
        $this->user = $user;

        return $this;
    }
}
