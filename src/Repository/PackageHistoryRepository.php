<?php

namespace App\Repository;

use App\DTO\PackageHistory as PackageHistoryDTO;
use App\Entity\Package;
use App\Entity\PackageHistory;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PackageHistoryRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method PackageHistory      create()
 * @method PackageHistoryDTO[] findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method PackageHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackageHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackageHistory[]    findAll()
 * @method PackageHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackageHistoryRepository extends AbstractRepository
{
    /**
     * PackageHistoryRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackageHistory::class, PackageHistoryDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('ph')
            ->innerJoin('ph.user', 'u')
            ->where('ph.package = :package');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('ph')
            ->innerJoin('ph.user', 'u')
            ->where('ph.package = :package');

        return $qb->select($qb->expr()->count('ph.id'));
    }

    /**
     * @param Package[]|int[] $packages
     * @return PackageHistory[]
     */
    public function findByPackages(array $packages): array
    {
        return $this->findBy(['package' => $packages]);
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOL
            ph.id,
            ph.transitionName,
            ph.description,
            ph.createdAt,
            u.id as user_id,
            u.lastName as user_lastName,
            u.firstName as user_firstName,
            u.phoneNumber as user_phoneNumber
        EOL;
    }
}
