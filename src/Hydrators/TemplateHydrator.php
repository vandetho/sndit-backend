<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\Template;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TemplateHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TemplateHydrator extends AbstractHydrator
{
    /**
     * TemplateHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Template::class);
    }
}
