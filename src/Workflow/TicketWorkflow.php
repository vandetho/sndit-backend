<?php
declare(strict_types=1);


namespace App\Workflow;


use App\Builder\TicketBuilder;
use App\Entity\Ticket;
use App\Entity\TicketAttachment;
use App\Form\Types\TicketMessageType;
use App\Repository\TicketMessageRepository;
use App\Repository\TicketRepository;
use App\Utils\RequestManipulator;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\Transition\TicketTransition;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TicketWorkflow
 *
 * @package App\Workflow
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketWorkflow extends AbstractWorkflow
{
    /**
     * @var TicketRepository
     */
    protected TicketRepository $ticketRepository;

    /**
     * @var WorkflowInterface
     */
    protected WorkflowInterface $ticketWorkflow;

    /**
     * @var TicketMessageRepository
     */
    protected TicketMessageRepository $ticketMessageRepository;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * TicketWorkflow constructor.
     *
     * @param Security                   $security
     * @param TranslatorInterface        $translator
     * @param ResponseGeneratorInterface $responseGenerator
     * @param WorkflowInterface          $ticketWorkflow
     * @param TicketRepository           $ticketRepository
     * @param TicketMessageRepository    $ticketMessageRepository
     * @param FormFactoryInterface       $formFactory
     */
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        ResponseGeneratorInterface $responseGenerator,
        WorkflowInterface $ticketWorkflow,
        TicketRepository $ticketRepository,
        TicketMessageRepository $ticketMessageRepository,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($security, $translator, $responseGenerator);
        $this->ticketWorkflow = $ticketWorkflow;
        $this->ticketRepository = $ticketRepository;
        $this->ticketMessageRepository = $ticketMessageRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function onTreat(Ticket $ticket): JsonResponse
    {
        if ($this->ticketWorkflow->can($ticket, TicketTransition::TREAT)) {
            $ticket->setInternalUser($this->getUser());
            $this->ticketWorkflow->apply($ticket, TicketTransition::TREAT);
            $this->ticketRepository->update($ticket);
            $message = $this->translator->trans('flash.success.ticket_treated', ['%idOrToken%' => $ticket->getToken()], 'application');

            return $this->responseGenerator->generateSuccess(TicketBuilder::buildDTO($ticket), $message);
        }
        $message = $this->translator->trans('flash.errors.ticket_treated', ['%idOrToken%' => $ticket->getToken()], 'application');

        return $this->responseGenerator->generateError($message);
    }

    /**
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function onReject(Ticket $ticket): JsonResponse
    {
        if ($this->ticketWorkflow->can($ticket, TicketTransition::REJECT)) {
            $this->ticketWorkflow->apply($ticket, TicketTransition::REJECT);
            $this->ticketRepository->update($ticket);
            $message = $this->translator->trans('flash.success.ticket_rejected', ['%idOrToken%' => $ticket->getToken()], 'application');

            return $this->responseGenerator->generateSuccess(TicketBuilder::buildDTO($ticket), $message);
        }
        $message = $this->translator->trans('flash.errors.ticket_rejected', ['%idOrToken%' => $ticket->getToken()], 'application');

        return $this->responseGenerator->generateError($message);
    }

    /**
     * @param Ticket  $ticket
     * @param Request $request
     * @return JsonResponse
     */
    public function onNeedFeedback(Ticket $ticket, Request $request): JsonResponse
    {
        if ($this->ticketWorkflow->can($ticket, TicketTransition::NEED_FEEDBACK)) {
            $ticketMessage = $this->ticketMessageRepository->create();
            $form = $this->formFactory->create(TicketMessageType::class, $ticketMessage);
            $form->submit(RequestManipulator::getData($request, $form));
            if ($form->isSubmitted() && $form->isValid()) {
                $ticketMessage->setTicket($ticket);
                $this->ticketWorkflow->apply($ticket, TicketTransition::NEED_FEEDBACK);
                $this->ticketRepository->save($ticketMessage);
                $this->ticketRepository->update($ticket);
                $message = $this->translator->trans('flash.success.ticket_need_feedback', ['%idOrToken%' => $ticket->getToken()], 'application');

                return $this->responseGenerator->generateSuccess(TicketBuilder::buildDTO($ticket), $message);
            }

            $message = $this->translator->trans('flash.errors.missing_parameters', [], 'application');

            return $this->responseGenerator->generateError($message);
        }
        $message = $this->translator->trans('flash.errors.ticket_need_feedback', ['%idOrToken%' => $ticket->getToken()], 'application');

        return $this->responseGenerator->generateError($message);
    }

    /**
     * @param Ticket  $ticket
     * @param Request $request
     * @return JsonResponse
     */
    public function onSubmitFeedback(Ticket $ticket, Request $request): JsonResponse
    {
        if ($this->ticketWorkflow->can($ticket, TicketTransition::SUBMIT_FEEDBACK)) {
            $ticketMessage = $this->ticketMessageRepository->create();
            $form = $this->formFactory->create(TicketMessageType::class, $ticketMessage);
            $form->submit(RequestManipulator::getData($request, $form));
            if ($form->isSubmitted() && $form->isValid()) {
                $ticketMessage->setTicket($ticket);
                $ticketMessage->getAttachments()->forAll(static function (TicketAttachment &$attachment) use ($ticket) {
                    $attachment->setTicket($ticket);
                });
                $this->ticketWorkflow->apply($ticket, TicketTransition::SUBMIT_FEEDBACK);
                $this->ticketRepository->save($ticketMessage);
                $this->ticketRepository->update($ticket);
                $message = $this->translator->trans('flash.success.ticket_message_sent', [], 'application');

                return $this->responseGenerator->generateSuccess(TicketBuilder::buildDTO($ticket), $message);
            }
            $message = $this->translator->trans('flash.errors.missing_parameters', [], 'application');

            return $this->responseGenerator->generateError($message);
        }
        $message = $this->translator->trans('flash.errors.ticket_need_feedback', ['%idOrToken%' => $ticket->getToken()], 'application');

        return $this->responseGenerator->generateError($message);
    }

    /**
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function onSolve(Ticket $ticket): JsonResponse
    {
        if ($this->ticketWorkflow->can($ticket, TicketTransition::SOLVE)) {
            $this->ticketWorkflow->apply($ticket, TicketTransition::SOLVE);
            $this->ticketRepository->update($ticket);
            $message = $this->translator->trans('flash.success.ticket_solved', ['%idOrToken%' => $ticket->getToken()], 'application');

            return $this->responseGenerator->generateSuccess(TicketBuilder::buildDTO($ticket), $message);
        }
        $message = $this->translator->trans('flash.errors.ticket_solved', ['%idOrToken%' => $ticket->getToken()], 'application');

        return $this->responseGenerator->generateError($message);
    }

    /**
     * @param Ticket $ticket
     * @param string $transition
     * @return JsonResponse
     */
    public function onClose(Ticket $ticket, string $transition): JsonResponse
    {
        if ($this->ticketWorkflow->can($ticket, $transition)) {
            $this->ticketWorkflow->apply($ticket, $transition);
            $this->ticketRepository->update($ticket);
            $message = $this->translator->trans('flash.success.ticket_closed', ['%idOrToken%' => $ticket->getToken()], 'application');

            return $this->responseGenerator->generateSuccess(TicketBuilder::buildDTO($ticket), $message);
        }
        $message = $this->translator->trans('flash.errors.ticket_closed', ['%idOrToken%' => $ticket->getToken()], 'application');

        return $this->responseGenerator->generateError($message);
    }
}
