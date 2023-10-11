<?php
declare(strict_types=1);



namespace App\Notifier;


use App\Model\NotificationMessage;

/**
 * Interface ExpoNotificationInterface
 *
 * @package App\Notifier
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
interface ExpoNotificationInterface
{

    /**
     * Handle the overall process of a new notification.
     *
     * @param string $to
     * @param string $message
     * @param string $title
     * @param array  $data
     *
     * @return NotificationMessage
     */
    public function sendNotification(string $to, string $message, string $title = '', array $data = []): NotificationMessage;

    /**
     * Handle the overall process of multiple new notifications.
     *
     * @param array $notifications
     *
     * @return NotificationMessage[]
     */
    public function sendNotifications(array $notifications): array;
}
