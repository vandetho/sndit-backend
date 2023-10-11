<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\Template;
use App\DTO\UserNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserNotificationMessageHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class UserNotificationMessageHydrator extends AbstractHydrator
{
    /**
     * UserNotificationMessageHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, UserNotificationMessage::class);
    }
}
