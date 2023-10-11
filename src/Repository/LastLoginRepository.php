<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\LastLogin as LastLoginDTO;
use App\Entity\LastLogin;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class LastLoginRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method LastLogin      create()
 * @method LastLoginDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method LastLogin|null find($id, $lockMode = null, $lockVersion = null)
 * @method LastLogin|null findOneBy(array $criteria, array $orderBy = null)
 * @method LastLogin[]    findAll()
 * @method LastLogin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LastLoginRepository extends AbstractRepository
{
    /**
     * LastLoginRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastLogin::class, LastLoginDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('ll');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('ll');

        return $qb->select($qb->expr()->count('ll.id'));

    }

    protected function getSelect(): string
    {
        return <<<EOT
            ll.id as id,
            ll.ip as ip,
            ll.device as device,
            ll.createdAt as createdAt
        EOT;
    }
}
