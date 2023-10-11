<?php
declare(strict_types=1);


namespace App\DTO;


/**
 * Class City
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class City extends AbstractDTO
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var Region|null
     */
    public ?Region $region = null;
}
