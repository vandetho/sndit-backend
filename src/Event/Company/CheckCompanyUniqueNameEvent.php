<?php
declare(strict_types=1);


namespace App\Event\Company;


use App\Entity\Company;
use App\Event\AbstractEvent;

/**
 * Class CheckCompanyUniqueNameEvent
 *
 * @package App\Event\Company
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckCompanyUniqueNameEvent extends AbstractEvent
{
    /**
     * @var Company
     */
    protected Company $company;

    /**
     * CheckCompanyUniqueCodeEvent constructor.
     *
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }
}
