<?php
declare(strict_types=1);


namespace App\DTO;


/**
 * Class UserNotificationToken
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class UserNotificationToken extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $token = null;

    /**
     * @var bool
     */
    public bool $valid = true;

    /**
     * @var string
     */
    public string $communicationType = 'expo';

    /**
     * @var User|null
     */
    public ?User $user = null;
}
