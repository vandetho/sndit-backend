<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\Package;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PackageHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageHydrator extends AbstractHydrator
{
    /**
     * PackageHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Package::class);
    }
}
