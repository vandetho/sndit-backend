<?php
declare(strict_types=1);


namespace App\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LocaleSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private string $defaultLocale;

    /**
     * LocaleSubscriber constructor.
     *
     * @param string $defaultLocale
     */
    public function __construct(string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Set a default locale onKernelRequest
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);

            return;
        }
        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
    }

    /**
     * {@inheritDoc}
     */
    #[ArrayShape([KernelEvents::REQUEST => "array[]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
