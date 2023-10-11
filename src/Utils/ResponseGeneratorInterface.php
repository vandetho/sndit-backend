<?php
declare(strict_types=1);


namespace App\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ResponseGeneratorInterface
 *
 * @package App\Utils
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
interface ResponseGeneratorInterface
{
    /**
     * Generate an error response
     *
     * @param string $message
     * @param int    $statusCode
     * @param array  $headers
     *
     * @return JsonResponse
     */
    public function generateError(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, array $headers = []): JsonResponse;

    /**
     * Generate an error response
     *
     * @param             $data
     * @param string|null $message
     * @param int         $statusCode
     * @param array       $headers
     * @param array       $context
     *
     * @return JsonResponse
     */
    public function generateSuccess($data = null, string $message = null, int $statusCode = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse;
}
