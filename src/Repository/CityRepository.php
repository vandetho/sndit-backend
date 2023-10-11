<?php
declare(strict_types=1);


namespace App\Repository;


use App\DTO\City as CityDTO;
use App\Entity\City;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CityRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method City      create()
 * @method CityDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends AbstractRepository
{
    /**
     * CityRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class, CityDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.region', 'r');

    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        return $qb->select($qb->expr()->count('c.id'))
            ->innerJoin('c.region', 'r');
    }

    /**
     * @return string
     */
    protected function getSelect(): string
    {
        return <<<EOT
            c.id as id,
            c.name as name,
            r.id as region_id,
            r.name as region_name,
            r.isoCountryCode as region_isoCountryCode
        EOT;

    }
}
