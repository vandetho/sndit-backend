<?php
declare(strict_types=1);


namespace App\DTO;


use JsonException;

/**
 * Class Company
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class Company extends AbstractDTO
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $canonicalName;

    /**
     * @var string
     */
    public string $token;

    /**
     * @var string[]
     */
    public array $roles;

    /**
     * @param string[]|string $roles
     * @throws JsonException
     */
    public function setRoles(array|string $roles): void
    {
        if (is_string($roles)) {
            $this->roles = json_decode($roles, true, 512, JSON_THROW_ON_ERROR);
            return;
        }
        $this->roles = $roles;
    }
}
