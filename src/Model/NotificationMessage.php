<?php
declare(strict_types=1);



namespace App\Model;


/**
 * Class NotificationMessage
 *
 * @package App\Model
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
final class NotificationMessage
{
    /**
     * @var string|null
     */
    private ?string $to;

    /**
     * @var string|null
     */
    private ?string $title;

    /**
     * @var string|null
     */
    private ?string $subtitle = null;

    /**
     * @var string|null
     */
    private ?string $body;

    /**
     * @var string|null
     */
    private ?string $priority;

    /**
     * @var array
     */
    private array $data;

    /**
     * @var string
     */
    private string $sound;

    /**
     * @var int|null
     */
    private ?int $ttl = null;

    /**
     * @var int|null
     */
    private ?int $expiration = null;

    /**
     * @var int|null
     */
    private ?int $badge = null;

    /**
     * @var bool
     */
    private bool $wasSuccessful = true;

    /**
     * @var string|null
     */
    private ?string $responseMessage = null;

    /**
     * @var array
     */
    private array $responseDetails = [];

    /**
     * @var string|null
     */
    private ?string $categoryId = null;

    /**
     * NotificationMessage constructor.
     *
     * @param string|null $to
     * @param string|null $title
     * @param string|null $body
     * @param string|null $priority
     * @param array       $data
     * @param string      $sound
     */
    public function __construct(?string $to = null, ?string $title = null, ?string $body = null, ?string $priority = 'normal', array $data = [], string $sound = 'default')
    {
        $this->to = $to;
        $this->title = $title;
        $this->body = $body;
        $this->priority = $priority;
        $this->data = $data;
        $this->sound = $sound;
    }

    /**
     * @return string|null
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * @param string|null $to
     * @return NotificationMessage
     */
    public function setTo(?string $to): NotificationMessage
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return NotificationMessage
     */
    public function setTitle(?string $title): NotificationMessage
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    /**
     * @param string|null $subtitle
     * @return NotificationMessage
     */
    public function setSubtitle(?string $subtitle): NotificationMessage
    {
        $this->subtitle = $subtitle;

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
     * @return NotificationMessage
     */
    public function setBody(?string $body): NotificationMessage
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @param string|null $priority
     * @return NotificationMessage
     */
    public function setPriority(?string $priority): NotificationMessage
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return NotificationMessage
     */
    public function setData(array $data): NotificationMessage
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getSound(): string
    {
        return $this->sound;
    }

    /**
     * @param string $sound
     * @return NotificationMessage
     */
    public function setSound(string $sound): NotificationMessage
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    /**
     * @param int|null $ttl
     * @return NotificationMessage
     */
    public function setTtl(?int $ttl): NotificationMessage
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getExpiration(): ?int
    {
        return $this->expiration;
    }

    /**
     * @param int|null $expiration
     * @return NotificationMessage
     */
    public function setExpiration(?int $expiration): NotificationMessage
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBadge(): ?int
    {
        return $this->badge;
    }

    /**
     * @param int|null $badge
     * @return NotificationMessage
     */
    public function setBadge(?int $badge): NotificationMessage
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWasSuccessful(): bool
    {
        return $this->wasSuccessful;
    }

    /**
     * @param bool $wasSuccessful
     * @return NotificationMessage
     */
    public function setWasSuccessful(bool $wasSuccessful): NotificationMessage
    {
        $this->wasSuccessful = $wasSuccessful;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResponseMessage(): ?string
    {
        return $this->responseMessage;
    }

    /**
     * @param string|null $responseMessage
     * @return NotificationMessage
     */
    public function setResponseMessage(?string $responseMessage): NotificationMessage
    {
        $this->responseMessage = $responseMessage;

        return $this;
    }

    /**
     * @return array
     */
    public function getResponseDetails(): array
    {
        return $this->responseDetails;
    }

    /**
     * @param array $responseDetails
     * @return NotificationMessage
     */
    public function setResponseDetails(array $responseDetails): NotificationMessage
    {
        $this->responseDetails = $responseDetails;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    /**
     * @param string|null $categoryId
     * @return NotificationMessage
     */
    public function setCategoryId(?string $categoryId): NotificationMessage
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get requestData
     *
     * @return array
     */
    public function getRequestData(): array
    {
        $result = [];

        if ($this->to) {
            $result['to'] = $this->to;
        }

        if ($this->data) {
            $result['data'] = $this->data;
        }

        if ($this->title) {
            $result['title'] = $this->title;
        }

        if ($this->body) {
            $result['body'] = $this->body;
        }

        if ($this->sound) {
            $result['sound'] = $this->sound;
        }

        if ($this->ttl) {
            $result['ttl'] = $this->ttl;
        }

        if ($this->expiration) {
            $result['expiration'] = $this->expiration;
        }

        if ($this->priority) {
            $result['priority'] = $this->priority;
        }

        if ($this->badge) {
            $result['badge'] = $this->badge;
        }

        if ($this->categoryId) {
            $result['categoryId'] = $this->categoryId;
        }

        return $result;
    }
}
