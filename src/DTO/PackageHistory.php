<?php

namespace App\DTO;

use DateTimeImmutable;
use Exception;

/**
 * Class PackageHistory
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageHistory extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $transitionName;

    /**
     * @var string|null
     */
    public ?string $description;

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
