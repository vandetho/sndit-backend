<?php
declare(strict_types=1);


namespace App\Model;

/**
 * Class ResponseData
 *
 * @package App\Model
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class ResponseData
{
    /**
     * @var bool
     */
    protected bool $error;

    /**
     * @var string|null
     */
    protected ?string $message;

    /**
     * ResponseData constructor.
     *
     * @param string|null $message
     * @param bool        $error
     */
    public function __construct(string $message = null, bool $error = false)
    {
        $this->error = $error;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @param bool $error
     * @return ResponseData
     */
    public function setError(bool $error): ResponseData
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ResponseData
     */
    public function setMessage(string $message): ResponseData
    {
        $this->message = $message;

        return $this;
    }
}
