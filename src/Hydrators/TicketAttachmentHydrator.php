<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\TicketAttachment;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TicketAttachmentHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketAttachmentHydrator extends AbstractHydrator
{
    /**
     * TicketHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, TicketAttachment::class);
    }
}
