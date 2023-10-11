<?php


namespace App\Utils;


use App\Entity\UserNotificationMessage;
use App\Notifier\ExpoNotificationInterface;
use App\Repository\UserNotificationTokenRepository;

/**
 * Class SendNotification
 *
 * @package App\Utils
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class SendNotification implements SendNotificationInterface
{
    /**
     * @var ExpoNotificationInterface
     */
    protected ExpoNotificationInterface $expoNotification;

    /**
     * @var UserNotificationTokenRepository
     */
    protected UserNotificationTokenRepository $userNotificationTokenRepository;

    /**
     * SendNotification constructor.
     *
     * @param ExpoNotificationInterface    $expoNotification
     * @param UserNotificationTokenRepository $userNotificationTokenRepository
     */
    public function __construct(ExpoNotificationInterface $expoNotification, UserNotificationTokenRepository $userNotificationTokenRepository)
    {
        $this->expoNotification = $expoNotification;
        $this->userNotificationTokenRepository = $userNotificationTokenRepository;
    }

    /**
     * @inheritDoc
     */
    public function send(array $notificationTokens, string $body, string $title, ?array $data = null): void
    {
        $notifications = [];

        foreach ($notificationTokens as $index => $token) {
            $message = new UserNotificationMessage();
            $message->setNotificationToken($token);
            $message->setBody($body);
            $message->setTitle($title);
            $message->setData($data);
            $notifications[] = ['to' => $token->getToken(), 'body' => $body, 'title' => $title, 'data' => $data];
            $this->userNotificationTokenRepository->save($message, false);
            if ($index % 30 === 0) {
                $this->userNotificationTokenRepository->flush();
            }
        }

        $this->userNotificationTokenRepository->flush();
        $this->expoNotification->sendNotifications($notifications);
    }
}
