<?php
declare(strict_types=1);


namespace App\DTO;


use DateTimeImmutable;
use Exception;

/**
 * Class TicketMessage
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketMessage extends AbstractDTO
{
    /**
     * @var string
     */
    public string $content;

    /**
     * @var array
     */
    public array $attachments = [];

    /**
     * @var DateTimeImmutable
     */
    public DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable
     */
    public DateTimeImmutable $updatedAt;

    /**
     * @var User|null
     */
    public ?User $user;

    /**
     * @var InternalUser|null
     */
    public ?InternalUser $internalUser;

    /**
     * @param DateTimeImmutable|string $createdAt
     * @return void
     * @throws Exception
     */
    public function setCreatedAt(DateTimeImmutable|string $createdAt): void
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTimeImmutable($createdAt);
            return;
        }
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTimeImmutable|string $createdAt
     * @return void
     * @throws Exception
     */
    public function setUpdatedAt(DateTimeImmutable|string $createdAt): void
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTimeImmutable($createdAt);
            return;
        }
        $this->createdAt = $createdAt;
    }
}
