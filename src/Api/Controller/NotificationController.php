<?php

namespace App\Api\Controller;


use App\DTO\Package;
use App\Repository\UserNotificationMessageRepository;
use Doctrine\ORM\NonUniqueResultException;
use OpenApi\Attributes as OA;
use App\Entity\UserNotificationMessage;
use App\Model\ErrorResponseData;
use App\Utils\ResponseGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class NotificationController
 *
 * @package App\Api\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[Security(name: "bearer")]
#[OA\Parameter(
    name: 'Authorization',
    description: 'JWT Token authentication',
    in: 'header',
    required: true,
    schema: new OA\Schema(type: 'string'),
)]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Return when user is not fully authenticated',
    content: new Model(type: ErrorResponseData::class)
)]
#[OA\Tag(name: 'Notification')]
#[Route(path: '/notifications', name: 'sndit_api_notifications_')]
class NotificationController extends AbstractController
{
    /**
     * @var UserNotificationMessageRepository
     */
    private UserNotificationMessageRepository $userNotificationMessageRepository;

    /**
     * NotificationController constructor.
     *
     * @param ResponseGeneratorInterface        $responseGenerator
     * @param TranslatorInterface               $translator
     * @param EventDispatcherInterface          $dispatcher
     * @param UserNotificationMessageRepository $userNotificationMessageRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        UserNotificationMessageRepository $userNotificationMessageRepository
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->userNotificationMessageRepository = $userNotificationMessageRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'offset',
        description: 'specify which data to start from retrieving data',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'The number of result per request',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(name: "sort", description: "Order column", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "order", description: "Order direction", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when user notifications are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[Route(name: 'gets', methods: ['GET'])]
    public function gets(Request $request): JsonResponse
    {
        $criteria = $this->getCriteria([], $request);
        $criteria['user'] = $this->getUser();

        return $this->generateTableData(
            $this->userNotificationMessageRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $this->userNotificationMessageRepository->countByCriteria($criteria),
            'notifications'
        );
    }
}
