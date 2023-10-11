<?php
declare(strict_types=1);


namespace App\Hydrators;

use App\DTO\InternalUser;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class InternalUserHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class InternalUserHydrator extends AbstractHydrator
{
    /**
     * InternalUserHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, InternalUser::class);
    }
}
