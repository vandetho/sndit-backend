<?php
declare(strict_types=1);


namespace App\Repository;


use App\DTO\Region as RegionDTO;
use App\Entity\Region;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class RegionRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method Region      create()
 * @method RegionDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method Region|null find($id, $lockMode = null, $lockVersion = null)
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionRepository extends AbstractRepository
{
    /**
     * RegionRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class, RegionDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('r');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r');
        return $qb->select($qb->expr()->count('r.id'));
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOT
            r.id as id,
            r.isoCountryCode as isoCountryCode
        EOT;
    }
}
