<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\InternalLastLogin as InternalLastLoginDTO;
use App\Entity\InternalLastLogin;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class InternalLastLoginRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method InternalLastLogin      create()
 * @method InternalLastLoginDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method InternalLastLogin|null find($id, $lockMode = null, $lockVersion = null)
 * @method InternalLastLogin|null findOneBy(array $criteria, array $orderBy = null)
 * @method InternalLastLogin[]    findAll()
 * @method InternalLastLogin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InternalLastLoginRepository extends AbstractRepository
{
    /**
     * InternalLastLoginRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InternalLastLogin::class, InternalLastLoginDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('ill');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('ill');

        return $qb->select($qb->expr()->count('ill.id'));

    }

    protected function getSelect(): string
    {
        return <<<EOT
            ill.id as id,
            ill.ip as ip,
            ill.device as device,
            ill.createdAt as createdAt
        EOT;
    }
}
