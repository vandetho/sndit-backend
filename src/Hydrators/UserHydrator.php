<?php
declare(strict_types=1);


namespace App\Hydrators;

use App\DTO\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class UserHydrator extends AbstractHydrator
{
    /**
     * UserHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, User::class);
    }
}
