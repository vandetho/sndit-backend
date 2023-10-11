<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\MonthlyReport;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class MonthlyReportHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class MonthlyReportHydrator extends AbstractHydrator
{
    public function __construct(EntityManagerInterface $em, ?string $dtoClass = MonthlyReport::class)
    {
        parent::__construct($em, $dtoClass);
    }
}
