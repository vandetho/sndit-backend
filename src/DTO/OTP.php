<?php
declare(strict_types=1);


namespace App\DTO;

/**
 * Class OTP
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class OTP extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $phoneNumber = null;

    /**
     * @var string|null The OTP request_id
     */
    public ?string $requestId = null;

    /**
     * @var string|null OTP price
     */
    public ?string $price = null;
}
