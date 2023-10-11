<?php
declare(strict_types=1);


namespace App\DTO;


use DateTimeImmutable;
use Exception;
use JsonException;

/**
 * Class Ticket
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class Ticket extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $name;

    /**
     * @var string|null
     */
    public ?string $token;

    /**
     * @var string|null
     */
    public ?string $email;

    /**
     * @var string|null
     */
    public ?string $phoneNumber;

    /**
     * @var string|null
     */
    public ?string $content;

    /**
     * @var DateTimeImmutable
     */
    public DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable
     */
    public DateTimeImmutable $updatedAt;

    /**
     * @var array
     */
    public array $marking = [];

    /**
     * @param array|string $marking
     * @return void
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
     * @param DateTimeImmutable|string $createAt
     * @return void
     * @throws Exception
     */
    public function setCreatedAt(DateTimeImmutable|string $createAt): void
    {
        if (is_string($createAt)) {
            $this->createdAt = new DateTimeImmutable($createAt);
            return;
        }
        $this->createdAt = $createAt;
    }

    /**
     * @param DateTimeImmutable|string $updatedAt
     * @return void
     * @throws Exception
     */
    public function setUpdated(DateTimeImmutable|string $updatedAt): void
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new DateTimeImmutable($updatedAt);
            return;
        }
        $this->updatedAt = $updatedAt;
    }
}
