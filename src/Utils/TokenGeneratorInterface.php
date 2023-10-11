<?php
declare(strict_types=1);


namespace App\Utils;

/**
 * Interface TokenGeneratorInterface
 *
 * @package App\Util
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
interface TokenGeneratorInterface
{
    /**
     * Generate a token
     *
     * @return string
     */
    public function generateToken(): string;

    /**
     * Generate a token without underscore
     *
     * @return string
     */
    public function generateTokenWithoutUnderscore(): string;

    /**
     * Generate an alphanumeric token
     *
     * @return string
     */
    public function generateAlphanumericToken(): string;

}
