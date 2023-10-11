<?php
declare(strict_types=1);


namespace App\Builder;


use App\DTO\City as CityDTO;
use App\Entity\City;
use JetBrains\PhpStorm\Pure;

/**
 * Class CityBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CityBuilder
{
    /**
     * @param City $city
     * @return CityDTO
     */
    #[Pure]
    public static function buildDTO(City $city): CityDTO
    {
        $dto = new CityDTO();
        $dto->id = $city->getId();
        $dto->name = $city->getName();
        return $dto;
    }
}
