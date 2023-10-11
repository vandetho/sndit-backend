<?php


namespace App\Utils;


use App\Entity\UserNotificationToken;

/**
 * Interface SendNotificationInterface
 *
 * @package App\Utils
 * @author Vandeth THO <thovandeth@gmail.com>
 */
interface SendNotificationInterface
{
    /**
     * Sending notification to users
     *
     * @param UserNotificationToken[] $notificationTokens
     * @param string                  $body
     * @param string                  $title
     * @param array|null              $data
     */
    public function send(array $notificationTokens, string $body, string $title, ?array $data = null): void;
}
