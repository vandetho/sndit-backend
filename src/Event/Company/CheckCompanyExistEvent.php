<?php
declare(strict_types=1);


namespace App\Event\Company;


use App\Entity\Company;
use App\Event\AbstractEvent;

/**
 * Class CheckCompanyExistEvent
 *
 * @package App\Event\Company
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckCompanyExistEvent extends AbstractEvent
{
    /**
     * @var int|string
     */
    private int|string $idOrToken;

    /**
     * @var Company
     */
    private Company $company;

    /**
     * CheckCompanyExistEvent constructor.
     *
     * @param int|string $idOrToken
     */
    public function __construct(int|string $idOrToken)
    {
        $this->idOrToken = $idOrToken;
    }

    /**
     * @return int|string
     */
    public function getIdOrToken(): int|string
    {
        return $this->idOrToken;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @param Company $company
     * @return CheckCompanyExistEvent
     */
    public function setCompany(Company $company): CheckCompanyExistEvent
    {
        $this->company = $company;

        return $this;
    }
}
