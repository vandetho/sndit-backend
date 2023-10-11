<?php
declare(strict_types=1);


namespace App\Builder;


use App\DTO\Template as TemplateDTO;
use App\Entity\Employee;
use App\Entity\Template;

/**
 * Class TemplateBuilder
 *
 * @template App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TemplateBuilder
{
    /**
     * @param Template      $template
     * @param Employee|null $employee
     * @return TemplateDTO
     */
    public static function buildDTO(Template $template, ?Employee $employee = null): TemplateDTO
    {
        $dto = new TemplateDTO();
        $dto->id = $template->getId();
        $dto->name = $template->getName();
        $dto->phoneNumber = $template->getPhoneNumber();
        $dto->address = $template->getAddress();
        $dto->city = CityBuilder::buildDTO($template->getCity());
        $dto->creator = UserBuilder::buildDTO($template->getCreator());
        $dto->company = CompanyBuilder::buildDTO($template->getCompany(), $employee);

        return $dto;
    }
}
