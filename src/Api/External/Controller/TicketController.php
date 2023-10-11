<?php
declare(strict_types=1);


namespace App\Api\External\Controller;

use App\Api\Controller\AbstractController;
use App\Builder\TicketBuilder;
use App\Builder\TicketMessageBuilder;
use App\DTO\Ticket;
use App\Entity\TicketAttachment;
use App\Event\Ticket\CheckTicketExistEvent;
use App\Form\Types\TicketAttachmentsType;
use App\Form\Types\TicketMessageType;
use App\Form\Types\TicketType;
use App\Model\ErrorResponseData;
use App\Repository\TicketAttachmentRepository;
use App\Repository\TicketMessageRepository;
use App\Repository\TicketRepository;
use App\Utils\RequestManipulator;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\TicketWorkflow;
use App\Workflow\Transition\TicketTransition;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Tag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class TicketController
 *
 * @package App\Api\External\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[Tag(name: 'External Ticket')]
#[Route(path: '/tickets', name: 'sndit_external_api_tickets_')]
class TicketController extends AbstractController
{
    /**
     * @var TicketRepository
     */
    protected TicketRepository $ticketRepository;

    /**
     * @var TicketWorkflow
     */
    protected TicketWorkflow $ticketWorkflow;

    /**
     * @var TicketMessageRepository
     */
    protected TicketMessageRepository $ticketMessageRepository;

    /**
     * @var TicketAttachmentRepository
     */
    private TicketAttachmentRepository $ticketAttachmentRepository;

    /**
     * TicketController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param TicketRepository           $ticketRepository
     * @param TicketWorkflow             $ticketWorkflow
     * @param TicketMessageRepository    $ticketMessageRepository
     * @param TicketAttachmentRepository $ticketAttachmentRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        TicketRepository $ticketRepository,
        TicketWorkflow $ticketWorkflow,
        TicketMessageRepository $ticketMessageRepository,
        TicketAttachmentRepository $ticketAttachmentRepository
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->ticketRepository = $ticketRepository;
        $this->ticketWorkflow = $ticketWorkflow;
        $this->ticketMessageRepository = $ticketMessageRepository;
        $this->ticketAttachmentRepository = $ticketAttachmentRepository;
    }

    /**
     * @param Request           $request
     * @param WorkflowInterface $ticketWorkflow
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: [new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'phoneNumber', type: 'string'),
                new OA\Property(property: 'content', type: 'string'),
                new OA\Property(property: 'files', type: 'array', items: new OA\Items(ref: new OA\Schema(type: 'string', format: 'binary'))),
            ])
        )]
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when ticket is created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when parameters are missing',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(name: 'post', methods: ['POST'])]
    public function post(Request $request, WorkflowInterface $ticketWorkflow): JsonResponse
    {
        $ticket = $this->ticketRepository->create();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->submit(RequestManipulator::getData($request, $form));
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setToken(Uuid::v4()->__toString());
            $ticketWorkflow->apply($ticket, TicketTransition::SUBMIT);

            $this->ticketRepository->save($ticket);

            return $this->generateSuccessResponse(TicketBuilder::buildDTO($ticket));
        }

        return $this->getMissingParamsResponse();
    }

    /**
     * @param string|int $idOrToken
     * @return JsonResponse|null
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when ticket is retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}', name: 'getc', methods: ['GET'])]
    public function getc(string|int $idOrToken): ?JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return $this->generateSuccessResponse(TicketBuilder::buildDTO($event->getTicket()));
    }

    /**
     * @param int|string $idOrToken
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when ticket is closed',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}/closes', name: 'closes', methods: ['POST'])]
    public function close(int|string $idOrToken): JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return $this->ticketWorkflow->onClose($event->getTicket(), TicketTransition::CLOSE);
    }

    /**
     * @param int|string $idOrToken
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when ticket is closed',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[Route(path: '/close-solved', name: 'close_solved', methods: ['POST'])]
    public function closeSolved(int|string $idOrToken): JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return $this->ticketWorkflow->onClose($event->getTicket(), TicketTransition::CLOSE_SOLVED);
    }

    /**
     * @param int|string     $idOrToken
     * @param Request        $request
     * @param UploaderHelper $uploaderHelper
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'content', type: 'string'),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when ticket message is created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}/messages', name: 'messages', methods: ['POST'])]
    public function messages(int|string $idOrToken, Request $request, UploaderHelper $uploaderHelper): JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $ticketMessage = $this->ticketMessageRepository->create();
        $form = $this->createForm(TicketMessageType::class, $ticketMessage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $event->getTicket();
            $ticketMessage->setTicket($event->getTicket());
            $ticketMessage->setUser($this->getUser());
            $attachments = $ticketMessage->getAttachments()->map(static function (TicketAttachment $attachment) use ($ticket) {
                $attachment->setTicket($ticket);
                return $attachment;
            });
            $ticketMessage->setAttachments($attachments);
            $this->ticketRepository->save($ticketMessage);
            $this->ticketRepository->update($ticket);
            $responseMessage = $this->translator->trans('flash.success.ticket_message_sent', [], 'application');

            return $this->responseGenerator->generateSuccess(TicketMessageBuilder::buildDTO($ticketMessage, $uploaderHelper), $responseMessage, Response::HTTP_CREATED);
        }

        return $this->getMissingParamsResponse();
    }

    /**
     * @param int|string     $idOrToken
     * @param Request        $request
     * @param UploaderHelper $uploaderHelper
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Parameter(name: "sort", description: "Order column", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "order", description: "Order direction", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(
        name: "offset",
        description: "specify which data to start from retrieving data",
        in: "query",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Parameter(
        name: "limit",
        description: "The number of result per request",
        in: "query",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when ticket message is created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', properties: [
                new OA\Property(
                    property: 'tickets',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Ticket::class))
                ),
                new OA\Property(property: 'totalRows', type: 'integer'),
            ], type: 'object'),
        ])
    )]
    #[Route(path: '/{idOrToken}/messages', name: 'get_messages', methods: ['GET'])]
    public function getMessages(int|string $idOrToken, Request $request, UploaderHelper $uploaderHelper): JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $criteria = $this->getCriteria([], $request);
        $criteria['ticket'] = $event->getTicket();
        $messages = $this->ticketMessageRepository->findByCriteria(
            $criteria,
            $this->getOrderBy($request),
            ...$this->getOffsetAndLimit($request)
        );
        $ids = [];
        foreach ($messages as $message) {
            $ids[$message->id] = $message;
        }
        if (count($ids) > 0) {
            $attachments = $this->ticketAttachmentRepository->findByMessages(array_keys($ids));
            foreach ($attachments as $attachment) {
                $id = $attachment->getMessage()?->getId();
                $ids[$id]->attachments[] = $uploaderHelper->asset($attachment, 'file');
            }
        }

        return $this->generateTableData(
            $messages,
            $this->ticketMessageRepository->countByCriteria($criteria),
            'messages'
        );
    }

    /**
     * @param int|string $idOrToken
     * @param Request    $request
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: [
            new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(properties: [
                    new OA\Property(property: 'files', type: 'array', items: new OA\Items(ref: new OA\Schema(type: 'string', format: 'binary'))),
                ])
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when ticket attachments are created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}/attachments', name: 'attachments', methods: ['POST'])]
    public function attachments(int|string $idOrToken, Request $request): JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $ticket = $event->getTicket();
        $form = $this->createForm(TicketAttachmentsType::class, $ticket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketRepository->update($ticket);
            $message = $this->translator->trans('flash.success.ticket_attachment_added', [], 'application');

            return $this->responseGenerator->generateSuccess(TicketBuilder::buildDTO($ticket), $message, Response::HTTP_CREATED);
        }

        return $this->getMissingParamsResponse();
    }

    /**
     * @param int|string $idOrToken
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when ticket is solved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}/solves', name: 'solves', methods: ['POST'])]
    public function solves(int|string $idOrToken): JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return $this->ticketWorkflow->onSolve($event->getTicket());
    }

    /**
     * @param int|string $idOrToken
     * @param Request    $request
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Ticket Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'content', type: 'string'),
                    new OA\Property(property: 'attachments', type: 'array', items: new OA\Items(ref: new OA\Schema(type: 'string', format: 'binary'))),
                ]
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when ticket is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when ticket is solved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Ticket::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}/submit-feedbacks', name: 'submit_feedbacks', methods: ['POST'])]
    public function submitFeedbacks(int|string $idOrToken, Request $request): JsonResponse
    {
        /** @var CheckTicketExistEvent $event */
        $event = $this->dispatchEvent(CheckTicketExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return $this->ticketWorkflow->onSubmitFeedback($event->getTicket(), $request);
    }
}
