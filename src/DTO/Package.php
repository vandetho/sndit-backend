<?php
declare(strict_types=1);


namespace App\DTO;


use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use JsonException;
use OpenApi\Attributes\Property;

/**
 * Class Package
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class Package extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $name;

    /**
     * @var string|null
     */
    public ?string $phoneNumber = null;

    /**
     * @var string|null
     */
    public ?string $token;

    /**
     * @var array
     */
    #[Property(property: 'marking', properties: [
        new Property(property: 'string', type: 'integer')
    ], type: 'object')]
    public array $marking = [];

    /**
     * @var string[]
     */
    public array $roles = [];

    /**
     * @var string|null
     */
    public ?string $address;

    /**
     * @var string|null
     */
    public ?string $note;

    /**
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $createdAt = null;

    /**
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $updatedAt = null;

    /**
     * @var float|null
     */
    public ?float $longitude;

    /**
     * @var float|null
     */
    public ?float $latitude;

    /**
     * @var City|null
     */
    public ?City $city = null;

    /**
     * @var User|null
     */
    public ?User $creator = null;

    /**
     * @var User|null
     */
    public ?User $user = null;

    /**
     * @var User|null
     */
    public ?User $deliverer = null;

    /**
     * @var Company|null
     */
    public ?Company $company = null;

    /**
     * @param DateTimeInterface|string|null $createdAt
     * @throws Exception
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt): void
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTimeImmutable($createdAt);
            return;
        }
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTimeInterface|string|null $updatedAt
     * @throws Exception
     */
    public function setUpdatedAt(DateTimeInterface|string|null $updatedAt): void
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new DateTimeImmutable($updatedAt);
            return;
        }
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param array|string $marking
     * @throws JsonException
     */
    public function setMarking(array|string $marking): void
    {
        if (is_string($marking)) {
            $this->marking = json_decode($marking, true, 512, JSON_THROW_ON_ERROR);

            return;
        }
        $this->marking = $marking;
    }

    /**
     * @param array|string $roles
     * @throws JsonException
     */
    public function setRoles(array|string $roles): void
    {
        if (is_string($roles)) {
            $this->roles = json_decode($roles, true, 512, JSON_THROW_ON_ERROR);

            return;
        }
        $this->roles = $roles;
    }
}
