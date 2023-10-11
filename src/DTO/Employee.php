<?php
declare(strict_types=1);


namespace App\DTO;

use DateTime;
use DateTimeInterface;
use Exception;
use JsonException;
use OpenApi\Attributes\Property;

/**
 * Class Employee
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class Employee extends AbstractDTO
{
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
     * @var string[]
     */
    public array $roles = [];

    /**
     * @var array
     */
    #[Property(property: 'marking', properties: [new Property(property: 'string', type: 'integer')], type: 'object')]
    public array $marking = [];

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
     * @param array|string|null $roles
     * @throws JsonException
     */
    public function setRoles(array|string|null $roles): void
    {
        if (is_array($roles)) {
            $this->roles = $roles;
            return;
        }
        $this->roles = json_decode($roles, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array|string|null $marking
     * @throws JsonException
     */
    public function setMarking(array|string|null $marking): void
    {
        if (is_array($marking)) {
            $this->marking = $marking;
            return;
        }
        $this->marking = json_decode($marking, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return "+$this->phoneNumber";
    }
}
