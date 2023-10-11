<?php

namespace App\Utils;

/**
 * Interface CanonicalizerInterface
 *
 * @package App\Utils
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
interface CanonicalizerInterface
{
    /**
     * @param $string
     *
     * @return string|null
     */
    public function canonicalize($string): ?string;
}
