<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class WorkflowLoggerSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class WorkflowLoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * WorkflowLoggerSubscriber constructor.
     *
     * @param Security $security
     */
    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
        ];
    }

    /**
     * @return User|UserInterface|null
     */
    protected function getUser(): UserInterface|User|null
    {
        return $this->security->getUser();
    }
}
