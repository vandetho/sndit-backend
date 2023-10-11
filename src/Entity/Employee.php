<?php
declare(strict_types=1);


namespace App\Entity;


use App\Constants\EmployeeRole;
use App\Repository\EmployeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Class Employee
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table(name: 'sndit_employee')]
class Employee extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'token', type: Types::STRING, unique: true, nullable: false)]
    private ?string $token = null;

    /**
     * @var array|string[]
     */
    #[ORM\Column(name: 'roles', type: Types::JSON, nullable: false)]
    private array $roles = [];

    /**
     * @var array
     */
    #[ORM\Column(name: 'marking', type: Types::JSON, nullable: false)]
    private array $marking = [];

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: true)]
    private ?User $creator = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    /**
     * @var Company|null
     */
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'employees')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true)]
    private ?Company $company = null;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array|string[] $roles
     * @return Employee
     */
    public function setRoles(array $roles): Employee
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return bool
     */
    #[Pure]
    public function isOwner(): bool
    {
        return $this->hasRole(EmployeeRole::ROLE_OWNER);
    }

    /**
     * @return bool
     */
    #[Pure]
    public function isManager(): bool
    {
        return $this->hasRole(EmployeeRole::ROLE_MANAGER);
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    /**
     * @return array
     */
    public function getMarking(): array
    {
        return $this->marking;
    }

    /**
     * @param array|string[] $marking
     * @return Employee
     */
    public function setMarking(array $marking): Employee
    {
        $this->marking = $marking;

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
     * @return Employee
     */
    public function setCreator(?User $creator): Employee
    {
        $this->creator = $creator;

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
     * @return Employee
     */
    public function setUser(?User $user): Employee
    {
        $this->user = $user;

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
     * @return Employee
     */
    public function setCompany(?Company $company): Employee
    {
        $this->company = $company;

        return $this;
    }

}
