<?php
declare(strict_types=1);


namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class NotificationCategory
 *
 * @package App\Entity
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'sndit_notification_category')]
class NotificationCategory extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: "name", type: Types::STRING, length: 150, unique: true, nullable: false)]
    private ?string $name = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return NotificationCategory
     */
    public function setName(?string $name): NotificationCategory
    {
        $this->name = $name;

        return $this;
    }


}
