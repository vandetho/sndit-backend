<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\City;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CityHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CityHydrator extends AbstractHydrator
{
    /**
     * CityHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, City::class);
    }
}
