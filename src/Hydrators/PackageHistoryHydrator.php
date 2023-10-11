<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\PackageHistory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PackageHistoryHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageHistoryHydrator extends AbstractHydrator
{
    /**
     * PackageHistoryHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, PackageHistory::class);
    }
}
