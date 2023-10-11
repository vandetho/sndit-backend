<?php
declare(strict_types=1);


namespace App\Event\Parameter;

use App\Event\AbstractEvent;

/**
 * Class ValidateParameterEvent
 *
 * @package App\Event\Parameter
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class ValidateParameterEvent extends AbstractEvent
{
    /**
     * @var array
     */
    private array $parameters;

    /**
     * @var string
     */
    private string $key;

    /**
     * ValidateParameterEvent constructor.
     *
     * @param array  $parameters
     * @param string $key
     */
    public function __construct(array $parameters, string $key)
    {
        $this->parameters = $parameters;
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
