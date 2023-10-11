<?php
declare(strict_types=1);


namespace App\Form\Types\Custom;


use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AbstractType
 *
 * @package App\Form\Types\Custom
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class AbstractType extends BaseAbstractType
{
    /**
     * @var Security
     */
    protected Security $security;

    /**
     * ResupplyItemType constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->security->getUser();
    }
}
