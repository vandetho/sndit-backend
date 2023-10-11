<?php
declare(strict_types=1);


namespace App\DTO;


/**
 * Class Region
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class Region extends AbstractDTO
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $isoCountryCode;
}
