<?php


namespace App\Utils;

/**
 * Class Canonicalizer
 *
 * @package App\Utils
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class Canonicalizer implements CanonicalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canonicalize($string): ?string
    {
        if (null === $string) {
            return null;
        }

        $encoding = mb_detect_encoding($string);

        return $encoding
            ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, MB_CASE_LOWER);
    }
}
