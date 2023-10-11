<?php
declare(strict_types=1);


namespace App\Event\Package;


use App\Entity\Package;
use App\Event\AbstractEvent;

/**
 * Class CheckPackageExistEvent
 *
 * @package App\Event\Package
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckPackageExistEvent extends AbstractEvent
{
    /**
     * @var int|string
     */
    private int|string $idOrToken;

    /**
     * @var Package
     */
    private Package $package;

    /**
     * CheckPackageExistEvent constructor.
     *
     * @param int|string $idOrToken
     */
    public function __construct(int|string $idOrToken)
    {
        $this->idOrToken = $idOrToken;
    }

    /**
     * @return int|string
     */
    public function getIdOrToken(): int|string
    {
        return $this->idOrToken;
    }

    /**
     * @return Package
     */
    public function getPackage(): Package
    {
        return $this->package;
    }

    /**
     * @param Package $package
     * @return CheckPackageExistEvent
     */
    public function setPackage(Package $package): CheckPackageExistEvent
    {
        $this->package = $package;

        return $this;
    }
}
