<?php
declare(strict_types=1);


namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class City
 *
 * @package App\Entity
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table(name: 'sndit_city')]
#[ORM\UniqueConstraint(name: 'unique_city_in_region', columns: ['name', 'region_id'])]
class City extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 150, nullable: false)]
    private ?string $name = null;

    /**
     * @var Region|null
     */
    #[ORM\ManyToOne(targetEntity: Region::class)]
    #[ORM\JoinColumn(name: 'region_id', referencedColumnName: 'id', nullable: false)]
    private ?Region $region = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return City
     */
    public function setName(string $name): City
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Region|null
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }

    /**
     * @param Region $region
     * @return City
     */
    public function setRegion(Region $region): City
    {
        $this->region = $region;

        return $this;
    }
}
