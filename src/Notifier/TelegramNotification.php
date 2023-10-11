<?php
declare(strict_types=1);



namespace App\Notifier;


use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Transport;

/**
 * Class TelegramNotification
 *
 * @package App\Notifierw
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TelegramNotification
{
    /**
     * @var string
     */
    protected string $dsn;

    /**
     * TelegramNotification constructor.
     *
     * @param string $dsn
     */
    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * Send a message to a chat room
     *
     * @param string $message
     * @param int    $chatId
     *
     * @throws TransportExceptionInterface
     */
    public function sendMessage(string $message, int $chatId): void
    {
        $transport = Transport::fromDsn($this->dsn.$chatId);
        $chat = (new ChatMessage($message));
        $transport->send($chat);
    }

}
