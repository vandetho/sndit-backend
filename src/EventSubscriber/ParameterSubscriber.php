<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Event\Parameter\CheckParameterEvent;
use App\Event\Parameter\ValidateParameterEvent;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ParameterSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class ParameterSubscriber extends AbstractSubscriber
{
    /**
     * @inheritDoc
     */
    #[ArrayShape([
        CheckParameterEvent::class    => "string",
        ValidateParameterEvent::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            CheckParameterEvent::class    => 'onCheckExist',
            ValidateParameterEvent::class => 'onValidate',
        ];
    }

    /**
     * This event is called to check if a parameter exist or not in an array
     *
     * @param CheckParameterEvent $event
     */
    public function onCheckExist(CheckParameterEvent $event): void
    {
        if (array_key_exists($event->getKey(), $event->getContent())) {
            return;
        }
        $message = $this->translator->trans('flash.errors.missing_parameters', [], 'application');
        $event->setResponse($this->responseGenerator->generateError($message));
    }

    /**
     * This event is called to check if the parameter value is valid or not
     *
     * @param ValidateParameterEvent $event
     */
    public function onValidate(ValidateParameterEvent $event): void
    {
        if (in_array($event->getKey(), $event->getParameters(), true)) {
            return;
        }
        $message = $this->translator->trans('flash.errors.missing_parameters', [], 'application');
        $event->setResponse($this->responseGenerator->generateError($message));
    }
}
