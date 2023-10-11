<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Entity\User;
use App\Utils\ResponseGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
abstract class AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @var ResponseGeneratorInterface
     */
    protected ResponseGeneratorInterface $responseGenerator;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var Security
     */
    protected Security $security;

    /**
     * AbstractSubscriber constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param Security                   $security
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security
    )
    {
        $this->responseGenerator = $responseGenerator;
        $this->translator = $translator;
        $this->security = $security;
    }

    /**
     * @return ResponseGeneratorInterface
     */
    public function getResponseGenerator(): ResponseGeneratorInterface
    {
        return $this->responseGenerator;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * @return User|UserInterface|null
     */
    protected function getUser(): ?User
    {
        return $this->security->getUser();
    }
}
