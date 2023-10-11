<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Class Package
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: PackageRepository::class)]
#[ORM\Table(name: 'sndit_package')]
class Package extends AbstractEntity
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
    #[ORM\Column(name: 'token', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $token = null;

    /**
     * @var array
     */
    #[ORM\Column(name: 'marking', type: Types::JSON, nullable: false)]
    private array $marking = [];

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'address', type: Types::STRING, length: 255, nullable: true)]
    private ?string $address = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'note', type: Types::STRING, length: 255, nullable: true)]
    private ?string $note = null;

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
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'deliverer_id', referencedColumnName: 'id', nullable: true)]
    private ?User $deliverer = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: false)]
    private ?User $creator = null;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(
        mappedBy: 'package',
        targetEntity: PackageImage::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $images;

    /**
     * Package constructor.
     */
    #[Pure]
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

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
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return Package
     */
    public function setToken(?string $token): Package
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return array
     */
    public function getMarking(): array
    {
        return $this->marking;
    }

    /**
     * @param array $marking
     * @return Package
     */
    public function setMarking(array $marking): Package
    {
        $this->marking = $marking;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     * @return Package
     */
    public function setNote(?string $note): Package
    {
        $this->note = $note;

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
     * @return Package
     */
    public function setAddress(?string $address): Package
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|null $latitude
     * @return Package
     */
    public function setLatitude(?float $latitude): Package
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
     * @return Package
     */
    public function setLongitude(?float $longitude): Package
    {
        $this->longitude = $longitude;

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
     * @return Package
     */
    public function setCity(?City $city): Package
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
     * @return Package
     */
    public function setCompany(?Company $company): Package
    {
        $this->company = $company;

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
     * @return Package
     */
    public function setUser(?User $user): Package
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getDeliverer(): ?User
    {
        return $this->deliverer;
    }

    /**
     * @param User|null $deliverer
     * @return Package
     */
    public function setDeliverer(?User $deliverer): Package
    {
        $this->deliverer = $deliverer;

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
     * @return Package
     */
    public function setCreator(?User $creator): Package
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getImages(): ArrayCollection|Collection
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection|Collection $images
     * @return Package
     */
    public function setImages(ArrayCollection|Collection $images): Package
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @param PackageImage $image
     * @return Package
     */
    public function addImage(PackageImage $image): Package
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setPackage($this);
        }

        return $this;
    }


    /**
     * @param PackageImage $image
     * @return Package
     */
    public function removeImage(PackageImage $image): Package
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            $image->setPackage(null);
        }

        return $this;
    }
}
