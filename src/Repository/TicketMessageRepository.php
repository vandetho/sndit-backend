<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\TicketMessage as TicketMessageDTO;
use App\Entity\TicketMessage;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TicketMessageRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method TicketMessage      create()
 * @method TicketMessageDTO[] findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method TicketMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketMessage[]    findAll()
 * @method TicketMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketMessageRepository extends AbstractRepository
{
    /**
     * TicketMessageRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketMessage::class, TicketMessageDTO::class);
    }

    /**
     * @return QueryBuilder
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('tm')
            ->leftJoin('tm.user', 'u')
            ->leftJoin('tm.internalUser', 'iu')
            ->where('tm.ticket = :ticket')
            ->orderBy('tm.createdAt', 'DESC');
    }

    /**
     * @return QueryBuilder
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('tm')
            ->where('tm.ticket = :ticket');

        return $qb->select($qb->expr()->countDistinct('tm.id'));
    }

    /**
     * @return string|array
     */
    protected function getSelect(): string|array
    {
        return <<<EOL
            tm.id,
            tm.content,
            tm.createdAt,
            tm.updatedAt,
            u.id as user_id,
            u.firstName as user_firstName,
            u.lastName as user_lastName,
            u.phoneNumber as user_phoneNumber,
            iu.id as internalUser_id,
            iu.firstName as internalUser_firstName,
            iu.lastName as internalUser_lastName,
            iu.emailCanonical as internalUser_email
        EOL;
    }

}
