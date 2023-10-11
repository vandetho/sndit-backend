<?php
declare(strict_types=1);


namespace App\Event\Template;


use App\Entity\Package;
use App\Event\AbstractEvent;

/**
 * Class CreateTemplateEvent
 *
 * @package App\Event\Template
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CreateTemplateEvent extends AbstractEvent
{
    /**
     * @var Package
     */
    private Package $package;

    /**
     * CreateTemplateEvent constructor.
     *
     * @param Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * @return Package
     */
    public function getPackage(): Package
    {
        return $this->package;
    }
}
