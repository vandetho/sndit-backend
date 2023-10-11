<?php

namespace App\Builder;


use App\DTO\Tracking as TrackingDTO;
use App\Entity\Tracking;

/**
 * Class TrackingBuilder
 *
 * @tracking App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TrackingBuilder
{

    /**
     * @param Tracking       $tracking
     * @return TrackingDTO
     */
    public static function buildDTO(Tracking $tracking): TrackingDTO
    {
        $dto = new TrackingDTO();
        $dto->id = $tracking->getId();
        $dto->latitude = $tracking->getLatitude();
        $dto->longitude = $tracking->getLongitude();
        $dto->createdAt = $tracking->getCreatedAt();
        $dto->user = UserBuilder::buildDTO($tracking->getUser());
        return $dto;
    }
}
