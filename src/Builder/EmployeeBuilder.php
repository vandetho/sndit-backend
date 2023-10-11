<?php
declare(strict_types=1);


namespace App\Builder;


use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\User;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class EmployeeBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class EmployeeBuilder
{
    /**
     * @param Employee $employee
     * @return \App\DTO\Employee
     */
    public static function buildDTO(Employee $employee): \App\DTO\Employee
    {
        /** @var User $user */
        $user = $employee->getUser();
        $dto = new \App\DTO\Employee();
        $dto->id = $employee->getId();
        $dto->token = $employee->getToken();
        $dto->marking = $employee->getMarking();
        $dto->roles = $employee->getRoles();
        $dto->phoneNumber = $user->getPhoneNumber();
        $dto->lastName = $user->getLastName();
        $dto->firstName = $user->getFirstName();
        $dto->dob = $user->getDob();
        $dto->telegramId = $user->getTelegramId();
        $dto->imageFile = $user->getImageFile();
        $dto->gender = $user->getGender();
        $dto->locale = $user->getLocale();

        return $dto;
    }
}
