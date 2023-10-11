<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\Company as CompanyDTO;
use App\Entity\Company;
use App\Entity\User;
use App\Workflow\Status\EmployeeStatus;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CompanyRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method Company      create()
 * @method CompanyDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class, CompanyDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.employees', 'e')
            ->where('e.user = :user');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select($qb->expr()->count('c.id'))
            ->innerJoin('c.employees', 'e')
            ->where('e.user = :user');
    }

    /**
     * @param string $token
     * @return Company|null
     */
    public function findByToken(string $token): ?Company
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @param User $user
     * @return Company[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.employees', 'e')
            ->where('e.user = :user')
            ->andWhere(sprintf("JSON_EXTRACT(e.marking, '$.%s') = 1", EmployeeStatus::ACTIVE))
            ->setParameters([
                'user' => $user,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User|int $user
     * @return Company[]
     */
    public function findByOwner(User|int $user): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameters([
                'user' => $user,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOL
            c.id,
            c.name,
            c.canonicalName,
            c.token,
            e.roles
        EOL;
    }

}
