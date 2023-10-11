<?php
declare(strict_types=1);


namespace App\DTO;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class NotificationCategory
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class NotificationCategory extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $name = null;
}
