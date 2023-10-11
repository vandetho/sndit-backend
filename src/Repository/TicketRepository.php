<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\Ticket as TicketDTO;
use App\Entity\Ticket;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TicketRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method Ticket      create()
 * @method TicketDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends AbstractRepository
{
    /**
     * TicketRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class, TicketDTO::class);
    }

    /**
     * @param string $token
     * @return Ticket|null
     */
    public function findByToken(string $token): ?Ticket
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @return QueryBuilder
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('t');
    }

    /**
     * @return QueryBuilder
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select($qb->expr()->countDistinct('t.id'));
    }

    /**
     * @return string|array
     */
    protected function getSelect(): string|array
    {
        return <<<EOL
            t.id,
            t.name,
            t.email,
            t.phoneNumber,
            t.content,
            t.createdAt,
            t.updatedAt,
            t.marking
        EOL;
    }

}
