<?php
declare(strict_types=1);


namespace App\Event\Ticket;


use App\Entity\Ticket;
use App\Event\AbstractEvent;

/**
 * Class CheckTicketExistEvent
 *
 * @package App\Event\Ticket
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckTicketExistEvent extends AbstractEvent
{
    /**
     * @var string|int
     */
    private string|int $idOrToken;

    /**
     * @var Ticket
     */
    private Ticket $ticket;

    /**
     * CheckTicketExistEvent constructor.
     *
     * @param int|string $idOrToken
     */
    public function __construct(int|string $idOrToken)
    {
        $this->idOrToken = $idOrToken;
    }

    /**
     * @return int|string
     */
    public function getIdOrToken(): int|string
    {
        return $this->idOrToken;
    }

    /**
     * @return Ticket
     */
    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    /**
     * @param Ticket $ticket
     * @return CheckTicketExistEvent
     */
    public function setTicket(Ticket $ticket): CheckTicketExistEvent
    {
        $this->ticket = $ticket;

        return $this;
    }
}
