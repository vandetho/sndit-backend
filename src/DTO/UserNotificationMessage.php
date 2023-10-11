<?php
declare(strict_types=1);


namespace App\DTO;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonException;

/**
 * Class UserNotificationMessage
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'sndit_user_notification_message')]
class UserNotificationMessage extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $title = null;

    /**
     * @var string|null
     */
    public ?string $body = null;

    /**
     * @var array|null
     */
    public ?array $data = [];

    /**
     * @var bool
     */
    public bool $isRead = false;

    /**
     * @var NotificationCategory|null
     */
    public ?NotificationCategory $category = null;

    /**
     * @param array|string $data
     * @return void
     * @throws JsonException
     */
    public function setData(array|string $data): void
    {
        if (is_string($data)) {
            $this->data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            return;
        }
        $this->data = $data;
    }
}
