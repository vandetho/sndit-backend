<?php
declare(strict_types=1);


namespace App\DTO;


/**
 * Class AbstractDTO
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
abstract class AbstractDTO
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AbstractDTO
     */
    public function setId(int $id): AbstractDTO
    {
        $this->id = $id;

        return $this;
    }
}
