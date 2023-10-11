<?php

namespace App\Repository;

use App\DTO\Tracking as TrackingDTO;
use App\Entity\Tracking;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TrackingRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method Tracking      create()
 * @method TrackingDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method Tracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracking[]    findAll()
 * @method Tracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackingRepository extends AbstractRepository
{
    /**
     * TrackingRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tracking::class, TrackingDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.user', 'u')
            ->where('u.id = :user');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.user', 'u')
            ->where('u.id = :user');

        return $qb->select($qb->expr()->count('t.id'));
    }

    /**
     * Find user last location
     *
     * @param User $user
     * @return TrackingDTO|null
     * @throws NonUniqueResultException
     */
    public function findLastByUser(User $user): ?TrackingDTO
    {
        return $this->createQueryBuilder('t')
            ->select($this->getSelect())
            ->innerJoin('t.user', 'u')
            ->where('u.id = :user')
            ->setParameters(['user' => $user])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(TrackingDTO::class);
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOL
            t.id,
            t.createdAt,
            t.latitude,
            t.longitude,
            u.id as user_id,
            u.firstName as user_firstName,
            u.lastName as user_lastName,
            u.phoneNumber as user_phoneNumber
        EOL;
    }
}
