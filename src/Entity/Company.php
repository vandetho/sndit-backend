<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Class Company
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'sndit_company')]
class Company extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $name;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'canonical_name', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $canonicalName;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'token', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $token;

    /**
     * @var array
     */
    #[ORM\Column(name: 'marking', type: Types::JSON, nullable: false)]
    private array $marking = [];

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Employee::class)]
    private Collection $employees;

    /**
     * Company constructor.
     */
    #[Pure]
    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCanonicalName(): ?string
    {
        return $this->canonicalName;
    }

    /**
     * @param string|null $canonicalName
     * @return Company
     */
    public function setCanonicalName(?string $canonicalName): Company
    {
        $this->canonicalName = $canonicalName;

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
     */
    public function setMarking(array $marking): void
    {
        $this->marking = $marking;
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
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
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

    /**
     * @return ArrayCollection|Collection
     */
    public function getEmployees(): ArrayCollection|Collection
    {
        return $this->employees;
    }

    /**
     * @param ArrayCollection|Collection $employees
     * @return Company
     */
    public function setEmployees(ArrayCollection|Collection $employees): Company
    {
        $this->employees = $employees;

        return $this;
    }
}
