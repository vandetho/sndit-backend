<?php

namespace App\Hydrators;

use App\DTO\Tracking;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TrackingHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TrackingHydrator extends AbstractHydrator
{
    /**
     * TrackingHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Tracking::class);
    }
}
