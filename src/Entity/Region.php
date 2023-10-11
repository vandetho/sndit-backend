<?php
declare(strict_types=1);


namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Region
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ORM\Table(name: 'sndit_region')]
#[ORM\Index(columns: ['iso_country_code'], name: 'region_country_code')]
#[ORM\UniqueConstraint(name: 'unique_region_in_country', columns: ['iso_country_code', 'name'])]
class Region extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 150, nullable: false)]
    private ?string $name = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'iso_country_code', type: Types::STRING, length: 2, nullable: false)]
    private ?string $isoCountryCode = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Region
     */
    public function setName(string $name): Region
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIsoCountryCode(): ?string
    {
        return $this->isoCountryCode;
    }

    /**
     * @param string|null $isoCountryCode
     */
    public function setIsoCountryCode(?string $isoCountryCode): void
    {
        $this->isoCountryCode = $isoCountryCode;
    }
}
