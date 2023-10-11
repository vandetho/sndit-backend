<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\TicketMessage;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TicketMessageHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketMessageHydrator extends AbstractHydrator
{
    /**
     * TicketHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, TicketMessage::class);
    }
}
