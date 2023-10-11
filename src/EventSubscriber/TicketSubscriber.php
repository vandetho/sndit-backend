<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Event\Ticket\CheckTicketExistEvent;
use App\Repository\TicketRepository;
use App\Utils\ResponseGeneratorInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TicketSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketSubscriber extends AbstractSubscriber
{
    /**
     * @var TicketRepository
     */
    protected TicketRepository $ticketRepository;

    /**
     * TicketSubscriber constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param Security                   $security
     * @param TicketRepository              $ticketRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security,
        TicketRepository $ticketRepository
    ) {
        parent::__construct($responseGenerator, $translator, $security);
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([CheckTicketExistEvent::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            CheckTicketExistEvent::class => 'onCheckExist',
        ];
    }

    public function onCheckExist(CheckTicketExistEvent $event): void
    {
        $idOrToken = $event->getIdOrToken();
        $ticket = is_numeric($idOrToken) ? $this->ticketRepository->find($idOrToken) : $this->ticketRepository->findByToken($idOrToken);
        if (null !== $ticket) {
            $event->setTicket($ticket);

            return;
        }
        $message = $this->translator->trans('flash.errors.ticket_not_found', ['%idOrToken%' => $idOrToken], 'application');
        $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_NOT_FOUND));
    }
}
