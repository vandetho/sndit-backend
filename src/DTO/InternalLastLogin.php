<?php
declare(strict_types=1);


namespace App\DTO;

use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Class InternalLastLogin
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class InternalLastLogin extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $ip = null;

    /**
     * @var string|null
     */
    public ?string $device = null;

    /**
     * @var DateTime|null
     */
    public ?DateTime $createdAt = null;

    /**
     * @param DateTimeInterface|string|null $createdAt
     * @throws Exception
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt): void
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTime($createdAt);
            return;
        }
        $this->createdAt = $createdAt;
    }
}
