<?php
declare(strict_types=1);


namespace App\Builder;


use App\DTO\Company as CompanyDTO;
use App\Entity\Company;
use App\Entity\Employee;
use JetBrains\PhpStorm\Pure;

/**
 * Class CompanyBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CompanyBuilder
{
    /**
     * @param Company       $company
     * @param Employee|null $employee
     * @return CompanyDTO
     */
    public static function buildDTO(Company $company, ?Employee $employee = null): CompanyDTO
    {
        $dto = new CompanyDTO();
        $dto->id = $company->getId();
        $dto->name = $company->getName();
        $dto->token = $company->getToken();
        if ($employee) {
            $dto->roles = $employee->getRoles();
        }

        return $dto;
    }

}
