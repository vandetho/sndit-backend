<?php
declare(strict_types=1);


namespace App\Workflow\Status;


/**
 * Class TicketStatus
 *
 * @package App\Workflow\State
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class TicketStatus
{
    public const SUBMIT_TICKET = 'submit_ticket';
    public const PENDING = 'pending';
    public const TREATING = 'treating';
    public const WAITING_FOR_FEEDBACK = 'waiting_for_feedback';
    public const RESOLVED = 'resolved';
    public const REJECTED = 'rejected';
    public const CLOSED = 'closed';
}
