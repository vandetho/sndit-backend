<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\Ticket;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TicketHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketHydrator extends AbstractHydrator
{
    /**
     * TicketHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Ticket::class);
    }
}
