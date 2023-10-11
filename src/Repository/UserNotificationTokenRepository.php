<?php
declare(strict_types=1);


namespace App\Repository;


use App\DTO\UserNotificationToken as UserNotificationTokenDTO;
use App\Entity\User;
use App\Entity\UserNotificationToken;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserNotificationTokenRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 *
 * @method UserNotificationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserNotificationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserNotificationToken[]    findAll()
 * @method UserNotificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserNotificationTokenDTO[] findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 */
class UserNotificationTokenRepository extends AbstractRepository
{
    /**
     * UserNotificationTokenRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotificationToken::class, UserNotificationTokenDTO::class);
    }

    /**
     * @param string $token
     *
     * @return UserNotificationToken|null
     */
    public function findByToken(string $token): ?UserNotificationToken
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @param User $user
     * @return UserNotificationToken|null
     */
    public function findByUser(User $user): ?UserNotificationToken
    {
        return $this->findOneBy(['user' => $user, 'valid' => true]);
    }

    /**
     * @param User[] $users
     * @return UserNotificationToken[]
     */
    public function findByUsers(array $users): array
    {
        return $this->findBy(['user' => $users, 'valid' => true]);
    }

    /**
     * @return QueryBuilder
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('unt')
            ->innerJoin('unt.user', 'u');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('unt')
            ->innerJoin('unt.user', 'u');

        return $qb->select($qb->expr()->count('unt.id'));
    }

    protected function getSelect(): string
    {
        return '';
    }
}
