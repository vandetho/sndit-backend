<?php
declare(strict_types=1);


namespace App\Builder;


use App\DTO\Ticket as TicketDTO;
use App\Entity\Ticket;

/**
 * Class TicketBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketBuilder
{
    /**
     * @param Ticket $ticket
     * @return TicketDTO
     */
    public static function buildDTO(Ticket $ticket): TicketDTO
    {
        $dto = new TicketDTO();
        $dto->id = $ticket->getId();
        $dto->name = $ticket->getName();
        $dto->token = $ticket->getToken();
        $dto->email = $ticket->getEmail();
        $dto->phoneNumber = $ticket->getPhoneNumber();
        $dto->content = $ticket->getContent();
        $dto->marking = $ticket->getMarking();
        $dto->createdAt = $ticket->getCreatedAt();
        $dto->updatedAt = $ticket->getUpdatedAt();
        return $dto;
    }
}
