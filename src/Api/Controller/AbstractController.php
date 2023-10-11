<?php
declare(strict_types=1);


namespace App\Api\Controller;


use App\Entity\InternalUser;
use App\Entity\User;
use App\Event\AbstractEvent;
use App\Event\Parameter\CheckParameterEvent;
use App\Event\Parameter\ValidateParameterEvent;
use App\Utils\ResponseGeneratorInterface;
use Closure;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractController
 *
 * @package App\Api\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method User|InternalUser|null getUser()
 */
abstract class AbstractController extends BaseAbstractController
{
    /**
     * @var ResponseGeneratorInterface
     */
    protected ResponseGeneratorInterface $responseGenerator;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $dispatcher;

    /**
     * @var PropertyAccessor
     */
    protected PropertyAccessor $propertyAccessor;

    /**
     * AbstractController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher
    ) {
        $this->responseGenerator = $responseGenerator;
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Return an array content by decoding the http request
     *
     * @param Request $request
     *
     * @param bool    $assoc
     * @param int     $depth
     * @param int     $options
     * @return array
     */
    protected function getContent(Request $request, bool $assoc = true, int $depth = 512, int $options = JSON_THROW_ON_ERROR): array
    {
        return json_decode($request->getContent(), $assoc, $depth, $options);
    }

    /**
     * @return JsonResponse
     */
    protected function getMissingParamsResponse(): JsonResponse
    {
        $message = $this->translator->trans('flash.errors.missing_parameters', [], 'application');

        return $this->responseGenerator->generateError($message);
    }

    /**
     * Dispatch an event subscriber
     *
     * @param string     $eventName
     * @param mixed|null $entity
     *
     * @return AbstractEvent
     */
    protected function dispatchEvent(string $eventName, mixed $entity = null): AbstractEvent
    {
        $event = new $eventName($entity);
        $this->dispatcher->dispatch($event);

        return $event;
    }

    /**
     * Dispatch an event subscriber
     *
     * @param string $eventName
     * @param array  $parameters
     *
     * @return AbstractEvent
     *
     * @throws ReflectionException
     */
    protected function dispatch(string $eventName, array $parameters): AbstractEvent
    {
        $reflection = new ReflectionClass($eventName);
        /** @var AbstractEvent $event */
        $event = $reflection->newInstanceArgs($parameters);
        $this->dispatcher->dispatch($event);

        return $event;
    }

    /**
     * Check whether a parameter key exist or not in content resource
     *
     * @param array  $content
     * @param string $key
     *
     * @return JsonResponse|null
     */
    protected function checkParameter(array $content, string $key): ?JsonResponse
    {
        $event = new CheckParameterEvent($content, $key);
        $this->dispatcher->dispatch($event);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return null;
    }

    /**
     * Check whether the parameter value is valid or not
     *
     * @param array  $parameters
     * @param string $key
     *
     * @return JsonResponse|null
     */
    protected function validateParameter(array $parameters, string $key): ?JsonResponse
    {
        $event = new ValidateParameterEvent($parameters, $key);
        $this->dispatcher->dispatch($event);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return null;
    }

    /**
     * Get the orderBy array from the request
     *
     * @param Request $request
     * @return array
     */
    protected function getOrderBy(Request $request): array
    {
        $orderBy = [];
        if ($request->query->get('sort')) {
            $orderBy[$request->query->get('sort')] = $request->query->get('order');
        }

        return $orderBy;
    }

    /**
     * Get the orderBy array from the request
     *
     * @param Request $request
     * @return int[]
     */
    protected function getOffsetAndLimit(Request $request): array
    {
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');
        if (!is_null($offset)) {
            $offset = (int)$offset;
        }
        if (!is_null($limit)) {
            $limit = (int)$limit;
        }

        return [$offset, $limit];
    }

    /**
     * Generate an error response
     *
     * @param string $message
     * @param int    $statusCode
     * @param array  $headers
     * @return JsonResponse
     */
    protected function generateErrorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, array $headers = []): JsonResponse
    {
        return $this->responseGenerator->generateError($message, $statusCode, $headers);
    }

    /**
     * Generate a json response for tables
     *
     * @param mixed  $data
     * @param int    $totalRow
     * @param string $dataSrc
     * @return JsonResponse
     */
    protected function generateTableData(mixed $data, int $totalRow, string $dataSrc = 'data'): JsonResponse
    {
        return $this->generateSuccessResponse([$dataSrc => $data, 'totalRows' => $totalRow]);
    }

    /**
     * Generate a success response
     *
     * @param             $data
     * @param string|null $message
     * @param int         $statusCode
     * @param array       $headers
     * @param array       $context
     *
     * @return JsonResponse
     */
    protected function generateSuccessResponse($data = null, string $message = null, int $statusCode = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse
    {
        return $this->responseGenerator->generateSuccess(
            $data,
            $message,
            $statusCode,
            $headers,
            $context
        );
    }


    /**
     * Get the table of criteria
     *
     * @param array   $parameters
     * @param Request $request
     *
     * @return array
     */
    protected function getCriteria(array $parameters, Request $request): array
    {
        $criteria = [];
        foreach ($parameters as $parameter => $value) {
            if (is_array($value)) {
                $defaultValue = $value['defaultValue'] ?? null;
                $criteria[$parameter] = $request->query->get($parameter, $defaultValue);
                if (array_key_exists('callback', $value) && $value['callback'] instanceof Closure && $criteria[$parameter] !== null) {
                    $criteria[$parameter] = $value['callback']($criteria[$parameter]);
                    continue;
                }
                continue;
            }
            if ($value instanceof Closure) {
                $criteria[$parameter] = $request->query->get($parameter);
                if ($criteria[$parameter] === null) {
                    $criteria[$parameter] = $value($request->query->get($parameter));
                }
                continue;
            }
            $criteria[$parameter] = $request->query->get($parameter, $value);

        }

        return $criteria;
    }

}
