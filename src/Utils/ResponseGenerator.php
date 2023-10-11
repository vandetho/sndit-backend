<?php
declare(strict_types=1);


namespace App\Utils;

use App\Model\ErrorResponseData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ResponseGenerator
 *
 * @package App\Utils
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class ResponseGenerator implements ResponseGeneratorInterface
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * ResponseGenerator constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function generateError(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, array $headers = []): JsonResponse
    {
        $data = new ErrorResponseData($message, $statusCode);
        return $this->json($data, $statusCode, $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function generateSuccess($data = null, string $message = null, int $statusCode = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse
    {
        return $this->json(['error' => false, 'message' => $message, 'data' => $data], $statusCode, $headers, $context);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param       $data
     * @param int   $status
     * @param array $headers
     * @param array $context
     *
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $json = $this->serializer->serialize($data, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        ], $context));

        return new JsonResponse($json, $status, $headers, true);
    }
}
