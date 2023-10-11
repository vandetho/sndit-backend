<?php

namespace App\DTO;


use DateTimeImmutable;
use Exception;

/**
 * Class Tracking
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class Tracking extends AbstractDTO
{
    /**
     * @var float|null
     */
    public ?float $latitude = null;

    /**
     * @var float|null
     */
    public ?float $longitude = null;

    /**
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $createdAt;

    /**
     * @var User|null
     */
    public ?User $user = null;

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
}
