<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\UserNotificationMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserNotificationMessage
 *
 * @package App\Entity
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: UserNotificationMessageRepository::class)]
#[ORM\Table(name: 'sndit_user_notification_message')]
class UserNotificationMessage extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'title', type: Types::STRING, length: 150, nullable: true)]
    private ?string $title = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'body', type: Types::STRING, length: 150, nullable: true)]
    private ?string $body = null;

    /**
     * @var array|null
     */
    #[ORM\Column(name: 'data', type: Types::JSON, nullable: true)]
    private ?array $data = [];

    /**
     * @var bool
     */
    #[ORM\Column(name: 'is_read', type: Types::BOOLEAN, nullable: false)]
    private bool $isRead = false;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'receipt_id', type: Types::STRING, length: 150, nullable: true)]
    private ?string $receiptId = null;

    /**
     * @var NotificationCategory|null
     */
    #[ORM\ManyToOne(targetEntity: NotificationCategory::class)]
    #[ORM\JoinColumn(name: 'notification_category_id', referencedColumnName: 'id', nullable: true)]
    private ?NotificationCategory $category = null;

    /**
     * @var UserNotificationToken|null
     */
    #[ORM\ManyToOne(targetEntity: UserNotificationToken::class)]
    #[ORM\JoinColumn(name: 'user_notification_token_id', referencedColumnName: 'id', nullable: true)]
    private ?UserNotificationToken $notificationToken = null;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return UserNotificationMessage
     */
    public function setTitle(?string $title): UserNotificationMessage
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     * @return UserNotificationMessage
     */
    public function setBody(?string $body): UserNotificationMessage
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     * @return UserNotificationMessage
     */
    public function setData(?array $data): UserNotificationMessage
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     * @return UserNotificationMessage
     */
    public function setRead(bool $isRead): UserNotificationMessage
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReceiptId(): ?string
    {
        return $this->receiptId;
    }

    /**
     * @param string|null $receiptId
     * @return UserNotificationMessage
     */
    public function setReceiptId(?string $receiptId): UserNotificationMessage
    {
        $this->receiptId = $receiptId;

        return $this;
    }

    /**
     * @return NotificationCategory|null
     */
    public function getCategory(): ?NotificationCategory
    {
        return $this->category;
    }

    /**
     * @param NotificationCategory|null $category
     * @return UserNotificationMessage
     */
    public function setCategory(?NotificationCategory $category): UserNotificationMessage
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return UserNotificationToken|null
     */
    public function getNotificationToken(): ?UserNotificationToken
    {
        return $this->notificationToken;
    }

    /**
     * @param UserNotificationToken|null $notificationToken
     * @return UserNotificationMessage
     */
    public function setNotificationToken(?UserNotificationToken $notificationToken): UserNotificationMessage
    {
        $this->notificationToken = $notificationToken;

        return $this;
    }
}
