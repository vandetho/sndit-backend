<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\Employee;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EmployeeHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class EmployeeHydrator extends AbstractHydrator
{
    /**
     * EmployeeHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Employee::class);
    }
}
