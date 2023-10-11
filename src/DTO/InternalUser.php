<?php
declare(strict_types=1);


namespace App\DTO;

use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Class InternalUser
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class InternalUser extends AbstractDTO
{
    /**
     * @var string|null 
     */
    public ?string $email;

    /**
     * @var string|null
     */
    public ?string $emailCanonical;

    /**
     * @var string|null
     */
    public ?string $firstName = null;

    /**
     * @var string|null
     */
    public ?string $lastName = null;

    /**
     * @var string|null
     */
    public ?string $phoneNumber = null;

    /**
     * @var string|null
     */
    public ?string $countryCode = null;

    /**
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $dob = null;

    /**
     * @var string|null
     */
    public ?string $gender = null;

    /**
     * @var string|null
     */
    public ?string $locale = null;

    /**
     * @var string|null
     */
    public ?string $telegramId = null;

    /**
     * @var string|null
     */
    public ?string $token = null;

    /**
     * @var string|null
     */
    public ?string $imageFile = null;

    /**
     * @var int[]
     */
    public array $companies = [];

    /**
     * @param DateTimeInterface|string|null $dob
     * @throws Exception
     */
    public function setDob(DateTimeInterface|string|null $dob): void
    {
        if (is_string($dob)) {
            $this->dob = new DateTime($dob);
            return;
        }
        $this->dob = $dob;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return "+$this->phoneNumber";
    }
}
