<?php

namespace App\Repository;

use App\Constants\EmployeeRole;
use App\DTO\Employee as EmployeeDTO;
use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class EmployeeRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method Employee      create()
 * @method EmployeeDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends AbstractRepository
{
    /**
     * StoreRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class, EmployeeDTO::class);
    }

    /**
     * @param Company $company
     * @param User    $user
     * @return Employee|null
     */
    public function findByCompanyAndUser(Company $company, User $user): ?Employee
    {
        return $this->findOneBy(['company' => $company, 'user' => $user]);
    }

    /**
     * @param Company $company
     * @param string  $role
     * @return Employee[]
     */
    public function findByCompanyAndRoles(Company $company, string $role): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.company = :company')
            ->andWhere("JSON_SEARCH(e.roles, 'one', :role) IS NOT NULL")
            ->setParameters([
                'company' => $company,
                'role'    => $role,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Company $company
     * @return Employee[]
     */
    public function findCompanyOwners(Company $company): array
    {
        return $this->findByCompanyAndRoles($company, EmployeeRole::ROLE_OWNER);
    }

    /**
     * @param Company $company
     * @return Employee[]
     */
    public function findCompanyManagers(Company $company): array
    {
        return $this->findByCompanyAndRoles($company, EmployeeRole::ROLE_MANAGER);
    }

    /**
     * @param Company $company
     * @param User    $user
     * @param string  $role
     * @return bool
     * @throws NonUniqueResultException
     */
    public function checkEmployeeRole(Company $company, User $user, string $role): bool
    {
        try {
            return $this->createQueryBuilder('e')
                ->select('1')
                ->where('e.company = :company')
                ->andWhere('e.user = :user')
                ->andWhere("JSON_SEARCH(e.roles, 'one', :role) IS NOT NULL")
                ->setParameters([
                    'company' => $company,
                    'user'    => $user,
                    'role'    => $role,
                ])
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return false;
        }
    }

    /**
     * @param Company $company
     * @param User    $user
     * @return bool
     * @throws NonUniqueResultException
     */
    public function isCompanyManager(Company $company, User $user): bool
    {
        return $this->checkEmployeeRole($company, $user, EmployeeRole::ROLE_MANAGER);
    }

    /**
     * @param Company $company
     * @param User    $user
     * @return bool
     * @throws NonUniqueResultException
     */
    public function isCompanyOwner(Company $company, User $user): bool
    {
        return $this->checkEmployeeRole($company, $user, EmployeeRole::ROLE_OWNER);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.user', 'eu')
            ->where('e.company = :company');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.user', 'eu')
            ->where('e.company = :company');

        return $qb->select($qb->expr()->count('e.id'));
    }

    /**
     * @param string $token
     * @return Employee|null
     */
    public function findByToken(string $token): ?Employee
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOL
            eu.id as id,
            eu.dob as dob,
            eu.firstName as firstName,
            eu.lastName as lastName,
            eu.gender as gender,
            eu.telegramId as telegramId,
            e.token as token,
            e.marking as marking,
            e.roles as roles,
            eu.imageName as imageFile,
            eu.locale as locale
        EOL;
    }
}
