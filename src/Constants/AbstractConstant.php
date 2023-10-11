<?php
declare(strict_types=1);


namespace App\Constants;


use ReflectionClass;

/**
 * Class AbstractConstant
 *
 * @package App\Constants
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
abstract class AbstractConstant
{
    /**
     * @return array
     */
    public static function getConstants(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
