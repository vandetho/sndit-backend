<?php
declare(strict_types=1);


namespace App\DTO;


use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Class Template
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class Template extends AbstractDTO
{
    /**
     * @var string|null
     */
    public ?string $name = null;

    /**
     * @var string|null
     */
    public ?string $phoneNumber = null;

    /**
     * @var string|null
     */
    public ?string $address = null;

    /**
     * @var City|null
     */
    public ?City $city = null;

    /**
     * @var Company|null
     */
    public ?Company $company = null;

    /**
     * @var User|null
     */
    public ?User $creator = null;
}
