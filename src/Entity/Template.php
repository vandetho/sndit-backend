<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\TemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Template
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[ORM\Table(name: 'sndit_template')]
class Template extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 150, nullable: false)]
    private ?string $name = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 30, nullable: true)]
    private ?string $phoneNumber = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'address', type: Types::STRING, length: 255, nullable: true)]
    private ?string $address = null;

    /**
     * @var City|null
     */
    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id', nullable: false)]
    private ?City $city = null;

    /**
     * @var Company|null
     */
    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false)]
    private ?Company $company = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: false)]
    private ?User $creator = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return Template
     */
    public function setPhoneNumber(?string $phoneNumber): Template
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return Template
     */
    public function setAddress(?string $address): Template
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     * @return Template
     */
    public function setCity(?City $city): Template
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param Company|null $company
     * @return Template
     */
    public function setCompany(?Company $company): Template
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * @param User|null $creator
     * @return Template
     */
    public function setCreator(?User $creator): Template
    {
        $this->creator = $creator;

        return $this;
    }
}
