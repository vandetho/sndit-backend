<?php
declare(strict_types=1);


namespace App\Repository;


use App\DTO\UserNotificationMessage as UserNotificationMessageDTO;
use App\Entity\User;
use App\Entity\UserNotificationMessage;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserNotificationMessageRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 *
 * @method UserNotificationMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserNotificationMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserNotificationMessage[]    findAll()
 * @method UserNotificationMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserNotificationMessageDTO[] findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 */
class UserNotificationMessageRepository extends AbstractRepository
{
    /**
     * UserNotificationMessageRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotificationMessage::class, UserNotificationMessageDTO::class);
    }

    /**
     * @param User $user
     * @return UserNotificationMessage[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('unm')
            ->innerJoin('unm.notificationToken', 'nt')
            ->where('nt.user = :user')
            ->setParameters(['user' => $user])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('unm')
            ->innerJoin('unm.notificationToken', 'nt')
            ->leftJoin('unm.category', 'c')
            ->where('nt.user = :user');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('unm')
            ->innerJoin('unm.notificationToken', 'nt')
            ->where('nt.user = :user');

        return $qb->select($qb->expr()->count('unm.id'));
    }

    protected function getSelect(): string
    {
        return <<<EOL
            unm.id,
            umn.createdAt,
            umn.title,
            umn.body,
            umn.data,
            umn.isRead,
            c.id as category_id,
            c.name as category_name
        EOL;
    }
}
