<?php
declare(strict_types=1);


namespace App\Model;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ErrorResponseData
 *
 * @package App\Model
 * @author  Vandeth THO <thovandeth@gmail.com>
 */
class ErrorResponseData extends ResponseData
{
    /**
     * @var integer
     */
    private int $code;

    /**
     * ErrorResponseData constructor.
     *
     * @param string|null $message
     * @param int         $code
     * @param bool        $error
     */
    #[Pure]
    public function __construct(string $message = null, int $code = Response::HTTP_BAD_REQUEST, bool $error = true)
    {
        parent::__construct($message, $error);
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return ErrorResponseData
     */
    public function setCode(int $code): ErrorResponseData
    {
        $this->code = $code;

        return $this;
    }
}
