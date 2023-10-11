<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\Company;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CompanyHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CompanyHydrator extends AbstractHydrator
{
    /**
     * CompanyHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Company::class);
    }
}
