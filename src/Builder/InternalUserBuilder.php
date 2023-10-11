<?php
declare(strict_types=1);


namespace App\Builder;


use App\Entity\Company;
use App\Entity\InternalUser;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class InternalUserBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class InternalUserBuilder
{
    /**
     * @param InternalUser        $user
     * @param UploaderHelper|null $uploaderHelper
     * @param Company[]           $companies
     * @return \App\DTO\InternalUser
     */
    public static function buildDTO(InternalUser $user, ?UploaderHelper $uploaderHelper = null, array $companies = []): \App\DTO\InternalUser
    {
        $dto = new \App\DTO\InternalUser();
        $dto->id = $user->getId();
        $dto->phoneNumber = $user->getSanitizePhoneNumber();
        $dto->email = $user->getEmailCanonical();
        $dto->lastName = $user->getLastName();
        $dto->firstName = $user->getFirstName();
        $dto->dob = $user->getDob();
        $dto->telegramId = $user->getTelegramId();
        $dto->token = $user->getToken();
        $dto->gender = $user->getGender();
        $dto->locale = $user->getLocale();
        $dto->imageFile = $uploaderHelper?->asset($user, 'imageFile');

        return $dto;
    }
}
