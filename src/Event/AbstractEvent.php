<?php
declare(strict_types=1);


namespace App\Event;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class AbstractEvent
 *
 * @package App\Event
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
abstract class AbstractEvent extends Event
{
    /**
     * @var JsonResponse|null
     */
    protected ?JsonResponse $response = null;

    /**
     * @return JsonResponse|null
     */
    public function getResponse(): ?JsonResponse
    {
        return $this->response;
    }

    /**
     * @param JsonResponse $response
     *
     * @return AbstractEvent
     */
    public function setResponse(JsonResponse $response): self
    {
        $this->response = $response;
        return $this;
    }
}
