<?php
declare(strict_types=1);


namespace App\Repository;

use App\Constants\EmployeeRole;
use App\DTO\MonthlyReport;
use App\DTO\Package as PackageDTO;
use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\Package;
use App\Entity\User;
use App\Workflow\Status\EmployeeStatus;
use App\Workflow\Status\PackageStatus;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PackageRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method Package      create()
 * @method PackageDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method Package|null find($id, $lockMode = null, $lockVersion = null)
 * @method Package|null findOneBy(array $criteria, array $orderBy = null)
 * @method Package[]    findAll()
 * @method Package[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackageRepository extends AbstractRepository
{
    /**
     * PackageRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class, PackageDTO::class);
    }

    /**
     * @param string $token
     * @return ?Package
     */
    public function findByToken(string $token): ?Package
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @param Company[]|int[] $companies
     * @return Package[]
     */
    public function findByCompanies(array $companies): array
    {
        return $this->findBy(['company' => $companies]);
    }

    /**
     * @param User|int $user
     * @param array    $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @return array
     */
    public function findWaitingForDeliveries(User|int $user, array $orderBy = [], int $offset = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select($this->getSelect())
            ->innerJoin('p.creator', 'cr')
            ->leftJoin('p.deliverer', 'd')
            ->leftJoin('p.user', 'u')
            ->innerJoin('p.company', 'c')
            ->leftJoin('c.employees', 'e', Join::WITH, 'e.user = :user')
            ->innerJoin('p.city', 'ct')
            ->where('p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
            ->andWhere('p.deliverer IS NULL')
            ->andWhere(sprintf("JSON_EXTRACT(p.marking, '$.%s') = 1", PackageStatus::WAITING_FOR_DELIVERY))
            ->andWhere($qb->expr()->in('p.company', $this->getUserCompaniesQueryBuilder()->getDQL()))
            ->setParameters([
                'user' => $user,
                'role' => EmployeeRole::ROLE_MANAGER,
            ])
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('p.id', 'DESC');
        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        return $qb->getQuery()
            ->getResult(PackageDTO::class);
    }

    /**
     * @param User|int $user
     * @return int
     * @throws NonUniqueResultException
     */
    public function countWaitingForDeliveries(User|int $user): int
    {
        $qb = $this->createQueryBuilder('p');

        try {
            return $qb->select($qb->expr()->count('p.id'))
                ->leftJoin('p.deliverer', 'd')
                ->leftJoin('p.user', 'u')
                ->innerJoin('p.city', 'ct')
                ->where('p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
                ->andWhere('p.deliverer IS NULL')
                ->andWhere(sprintf("JSON_EXTRACT(p.marking, '$.%s') = 1", PackageStatus::WAITING_FOR_DELIVERY))
                ->andWhere($qb->expr()->in('p.company', $this->getUserCompaniesQueryBuilder()->getDQL()))
                ->setParameters([
                    'user' => $user,
                    'role' => EmployeeRole::ROLE_MANAGER,
                ])
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * @param Employee $employee
     * @return bool
     * @throws NonUniqueResultException
     */
    public function employeeHasOnDeliveries(Employee $employee): bool
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('1')
            ->innerJoin('p.deliverer', 'd')
            ->innerJoin('p.company', 'c')
            ->innerJoin('c.employees', 'e')
            ->where('p.deliverer = :user')
            ->andWhere('e.id = :employee')
            ->andWhere(sprintf("JSON_EXTRACT(p.marking, '$.%s') = 1", PackageStatus::ON_DELIVERY))
            ->setParameters([
                'user'     => $employee->getUser(),
                'employee' => $employee,
            ]);

        try {
            return $qb->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     * @param User|int $user
     * @param array    $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @return array
     */
    public function findOnDeliveries(User|int $user, array $orderBy = [], int $offset = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select($this->getSelect())
            ->innerJoin('p.creator', 'cr')
            ->innerJoin('p.deliverer', 'd')
            ->leftJoin('p.user', 'u')
            ->innerJoin('p.company', 'c')
            ->leftJoin('c.employees', 'e', Join::WITH, 'e.user = :user')
            ->innerJoin('p.city', 'ct')
            ->where('p.deliverer = :user OR p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
            ->andWhere(sprintf("JSON_EXTRACT(p.marking, '$.%s') = 1", PackageStatus::ON_DELIVERY))
            ->andWhere($qb->expr()->in('p.company', $this->getUserCompaniesQueryBuilder()->getDQL()))
            ->setParameters([
                'user' => $user,
                'role' => EmployeeRole::ROLE_MANAGER,
            ])
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('p.id', 'DESC');
        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        return $qb->getQuery()
            ->getResult(PackageDTO::class);
    }

    /**
     * @param User|int $user
     * @return int
     * @throws NonUniqueResultException
     */
    public function countOnDeliveries(User|int $user): int
    {
        $qb = $this->createQueryBuilder('p');

        try {
            return $qb->select($qb->expr()->count('p.id'))
                ->innerJoin('p.deliverer', 'd')
                ->leftJoin('p.user', 'u')
                ->innerJoin('p.city', 'ct')
                ->where('p.deliverer = :user OR p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
                ->andWhere(sprintf("JSON_EXTRACT(p.marking, '$.%s') = 1", PackageStatus::ON_DELIVERY))
                ->andWhere($qb->expr()->in('p.company', $this->getUserCompaniesQueryBuilder()->getDQL()))
                ->setParameters([
                    'user' => $user,
                    'role' => EmployeeRole::ROLE_MANAGER,
                ])
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * @param string $state
     * @return Package|null
     * @throws NonUniqueResultException
     */
    public function findOneByState(string $state): ?Package
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->leftJoin('p.deliverer', 'd')
            ->innerJoin('p.city', 'ct')
            ->leftJoin('p.user', 'u')
            ->where(sprintf("JSON_EXTRACT(p.marking, '$.%s') = 1", $state))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->select($this->getSelect())
            ->innerJoin('p.creator', 'cr')
            ->leftJoin('p.deliverer', 'd')
            ->leftJoin('p.city', 'ct')
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.company', 'c')
            ->leftJoin('c.employees', 'e', Join::WITH, 'e.user = :user AND '.sprintf("JSON_EXTRACT(e.marking, '$.%s') = 1", EmployeeStatus::ACTIVE))
            ->where('p.deliverer = :user OR p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
            ->andWhere($qb->expr()->in('p.company', $this->getUserCompaniesQueryBuilder()->getDQL()))
            ->orderBy('p.id', 'DESC');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->select($qb->expr()->count('DISTINCT p.id'))
            ->leftJoin('p.deliverer', 'd')
            ->leftJoin('p.company', 'c')
            ->where('p.deliverer = :user OR p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
            ->andWhere($qb->expr()->in('p.company', $this->getUserCompaniesQueryBuilder()->getDQL()));
    }


    /**
     * @param Company  $company
     * @param User     $user
     * @param array    $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @return PackageDTO[]
     */
    public function findByCompanyAndUser(Company $company, User $user, array $orderBy = [], int $offset = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters(['company' => $company, 'user' => $user, 'role' => EmployeeRole::ROLE_MANAGER]);

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        return $qb->select($this->getSelect())
            ->innerJoin('p.creator', 'cr')
            ->leftJoin('p.deliverer', 'd')
            ->innerJoin('p.city', 'ct')
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.company', 'c')
            ->leftJoin('c.employees', 'e', Join::WITH, 'e.user = :user AND '.sprintf("JSON_EXTRACT(e.marking, '$.%s') = 1", EmployeeStatus::ACTIVE))
            ->where('p.deliverer = :user OR p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
            ->andWhere('p.company = :company')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult($this->dtoClass);
    }

    /**
     * @param Company $company
     * @param User    $user
     * @return int
     * @throws NonUniqueResultException
     */
    public function countByCompanyAndUser(Company $company, User $user): int
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select($qb->expr()->count('DISTINCT p.id'))
            ->leftJoin('p.deliverer', 'd')
            ->innerJoin('p.city', 'ct')
            ->leftJoin('p.company', 'c')
            ->where('p.deliverer = :user OR p.user = :user OR '.$qb->expr()->exists($this->getRoleManagerPackageQueryBuilder()->getDQL()))
            ->andWhere('p.company = :company');

        try {
            return $qb->setParameters(['company' => $company, 'user' => $user, 'role' => EmployeeRole::ROLE_MANAGER])
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * Get current month total packages
     *
     * @return MonthlyReport[]
     */
    public function countMonthly(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('YEAR(p.createdAt) = YEAR(CURRENT_DATE())')
            ->groupBy('month');

        return $qb->select([
            'MONTH(p.createdAt) as month',
            $qb->expr()->count('p.id').' as total',
        ])->getQuery()
            ->getResult(MonthlyReport::class);
    }

    /**
     * Get current month total packages
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function countCurrentYear(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->where('YEAR(p.createdAt) = YEAR(CURRENT_DATE())');
        try {
            return $qb->select($qb->expr()->count('p.id'))
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * Get current month total packages
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function countCurrentMonth(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->where('MONTH(p.createdAt) = MONTH(CURRENT_DATE())')
            ->andWhere('YEAR(p.createdAt) = YEAR(CURRENT_DATE())');
        try {
            return $qb->select($qb->expr()->count('p.id'))
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function countAverageMonthly(): array
    {
        $sql = <<<EOL
            SELECT AVG(sn.`total`) as `total`, sn.`month` 
            FROM (
                SELECT MONTH(p.created_at) as `month`, YEAR(p.created_at) as `year`, COUNT(p.id) as `total` 
                FROM sndit_package p 
                WHERE YEAR(p.created_at) = YEAR(CURRENT_DATE()) - 1
                GROUP BY `month`, `year`
            ) sn GROUP BY `month`
        EOL;
        $stmt = $this->_em->getConnection()->prepare($sql);

        return $stmt->executeQuery([])->fetchAllAssociative();
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    public function countMonthlyPerCompanies(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->where('MONTH(p.createdAt) = MONTH(CURRENT_DATE())')
            ->andWhere('YEAR(p.createdAt) = YEAR(CURRENT_DATE())');
        try {
            return $qb->select($qb->expr()->count('p.id'))
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * @return QueryBuilder
     */
    public function getRoleManagerPackageQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('sp')
            ->select('sp.id')
            ->innerJoin('sp.company', 'spc')
            ->innerJoin('spc.employees', 'spce')
            ->where('spce.user = :user')
            ->andWhere("JSON_SEARCH(spce.roles, 'one', :role) IS NOT NULL");
    }


    /**
     * @return QueryBuilder
     */
    public function getUserCompaniesQueryBuilder(): QueryBuilder
    {
        return $this->_em->createQueryBuilder()
            ->select('ssc.id')
            ->from('App:Company', 'ssc')
            ->innerJoin('ssc.employees', 'ssce')
            ->where('ssce.user = :user');
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOL
            DISTINCT p.id,
            p.name,
            p.token,
            p.note,
            p.address,
            p.createdAt,
            p.updatedAt,
            p.latitude,
            p.longitude,
            p.updatedAt,
            p.marking,
            e.roles,
            c.id as company_id,
            c.name as company_name,
            c.canonicalName as company_canonicalName,
            c.token as company_token,
            ct.id as city_id,
            ct.name as city_name,
            cr.id as creator_id,
            cr.firstName as creator_firstName,
            cr.lastName as creator_lastName,
            cr.phoneNumber as creator_phoneNumber,
            d.id as deliverer_id,
            d.firstName as deliverer_firstName,
            d.lastName as deliverer_lastName,
            d.phoneNumber as deliverer_phoneNumber,
            u.id as user_id,
            u.firstName as user_firstName,
            u.lastName as user_lastName,
            u.phoneNumber as user_phoneNumber
        EOL;
    }
}
