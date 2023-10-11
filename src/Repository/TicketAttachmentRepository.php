<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\TicketAttachment as TicketAttachmentDTO;
use App\Entity\Ticket;
use App\Entity\TicketAttachment;
use App\Entity\TicketMessage;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TicketAttachmentRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method TicketAttachment      create()
 * @method TicketAttachmentDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method TicketAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketAttachment[]    findAll()
 * @method TicketAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketAttachmentRepository extends AbstractRepository
{
    /**
     * TicketAttachmentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketAttachment::class, TicketAttachmentDTO::class);
    }

    /**
     * @return QueryBuilder
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('ta');
    }

    /**
     * @return QueryBuilder
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('ta');

        return $qb->select($qb->expr()->countDistinct('ta.id'));
    }

    /**
     * @param int[]|TicketMessage[] $messages
     * @return TicketAttachment[]
     */
    public function findByMessages(array $messages): array
    {
        return $this->createQueryBuilder('ta')
            ->addSelect('m')
            ->leftJoin('ta.message', 'm')
            ->where('ta.message IN (:messages)')
            ->setParameters(['messages' => $messages])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int[]|Ticket[] $tickets
     * @return TicketAttachment[]
     */
    public function findByTickets(array $tickets): array
    {
        return $this->createQueryBuilder('ta')
            ->where('ta.ticket IN (:tickets)')
            ->setParameters(['tickets' => $tickets])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int[]|Ticket[] $tickets
     * @return TicketAttachment[]
     */
    public function findByTicketsWithoutMessages(array $tickets): array
    {
        return $this->createQueryBuilder('ta')
            ->where('ta.ticket IN (:tickets)')
            ->andWhere('ta.message IS NULL')
            ->setParameters(['tickets' => $tickets])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string|array
     */
    protected function getSelect(): string|array
    {
        return <<<EOL
            ta.id,
            ta.filename,
        EOL;
    }

}
