<?php
declare(strict_types=1);


namespace App\Event\Parameter;


use App\Event\AbstractEvent;

/**
 * Class CheckParameterEvent
 *
 * @package App\Event\Parameter
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class CheckParameterEvent extends AbstractEvent
{
    /**
     * @var array
     */
    private array $content;

    /**
     * @var string
     */
    private string $key;

    /**
     * CheckParameterEvent constructor.
     *
     * @param array  $content
     * @param string $key
     */
    public function __construct(array $content, string $key)
    {
        $this->content = $content;
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
