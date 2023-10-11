<?php
declare(strict_types=1);


namespace App\Builder;


use App\Entity\Company;
use App\Entity\User;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class UserBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class UserBuilder
{
    /**
     * @param User                $user
     * @param UploaderHelper|null $uploaderHelper
     * @param Company[]           $companies
     * @return \App\DTO\User
     */
    public static function buildDTO(User $user, ?UploaderHelper $uploaderHelper = null, array $companies = []): \App\DTO\User
    {
        $dto = new \App\DTO\User();
        $dto->id = $user->getId();
        $dto->phoneNumber = $user->getSanitizePhoneNumber();
        $dto->lastName = $user->getLastName();
        $dto->firstName = $user->getFirstName();
        $dto->dob = $user->getDob();
        $dto->telegramId = $user->getTelegramId();
        $dto->token = $user->getToken();
        $dto->gender = $user->getGender();
        $dto->locale = $user->getLocale();
        $dto->imageFile = $uploaderHelper?->asset($user, 'imageFile');
        foreach ($companies as $company) {
            $dto->companies[] = $company->getId();
        }

        return $dto;
    }
}
