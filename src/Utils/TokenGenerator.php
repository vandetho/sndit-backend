<?php
declare(strict_types=1);


namespace App\Utils;

use Exception;

/**
 * Class TokenGenerator
 *
 * @package App\Util
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @var integer
     */
    private int $entropy;

    /**
     * TokenGenerator constructor.
     *
     * @param int $entropy
     */
    public function __construct(int $entropy = 256)
    {
        $this->entropy = $entropy;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function generateToken(): string
    {
        $bytes = random_bytes($this->entropy / 8);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function generateTokenWithoutUnderscore(): string
    {
        return bin2hex(random_bytes($this->entropy / 8));
    }

    /**
     * {@inheritDoc}
     */
    public function generateAlphanumericToken(): string
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';

        return substr(str_shuffle($data), 0, $this->entropy / 8);
    }

    /**
     * @param array{numeric: boolean, majuscule: boolean, minuscule: boolean, symbols: boolean, length: integer } $options
     * @return string
     */
    public static function generate(array $options): string {
        $options = array_merge([
            'numeric'   => true,
            'majuscule' => true,
            'minuscule'   => true,
            'symbols' => true,
            'length' => 16
        ], $options);

        $numeric = '1234567890';
        $majuscule = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $minuscule = 'abcefghijklmnopqrstuvwxyz';
        $symbols = '@#$%';

        $data = '';
        if ($options['numeric']) {
            $data .= $numeric;
        }
        if ($options['majuscule']) {
            $data .= $majuscule;
        }
        if ($options['minuscule']) {
            $data .= $minuscule;
        }
        if ($options['symbols']) {
            $data .= $symbols;
        }

        return substr(str_shuffle($data), 0, $options['length']);
    }


}
