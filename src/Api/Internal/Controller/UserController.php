<?php

namespace App\Api\Internal\Controller;

use App\Api\Controller\AbstractController;
use App\DTO\User;
use App\Event\User\CheckUserExistEvent;
use App\Event\User\DeleteUserEvent;
use App\Model\ErrorResponseData;
use App\Repository\CompanyRepository;
use App\Repository\EmployeeRepository;
use App\Repository\InternalUserRepository;
use App\Repository\PackageHistoryRepository;
use App\Repository\PackageRepository;
use App\Repository\UserRepository;
use App\Utils\ResponseGeneratorInterface;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 *
 * @package App\Api\Internal\Controller
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
#[OA\Tag(name: 'Internal Users')]
#[Route(path: '/users', name: 'sndit_internal_api_users_')]
class UserController extends AbstractController
{
    /**
     * @var InternalUserRepository
     */
    private InternalUserRepository $internalUserRepository;

    /**
     * UserController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param InternalUserRepository     $internalUserRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        InternalUserRepository $internalUserRepository
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->internalUserRepository = $internalUserRepository;
    }

    /**
     * @param Request        $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when the list of tickets is found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'bool', enum: [false]),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'data', properties: [
                    new OA\Property(
                        property: 'users',
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: User::class))
                    ),
                    new OA\Property(property: 'totalRows', type: 'integer'),
                ], type: 'object'),
            ]
        )
    )]
    public function delete(Request $request, UserRepository $userRepository): JsonResponse
    {
        $criteria = $this->getCriteria([], $request);

        return $this->generateTableData(
            $userRepository->findDeletedByCriteria(
                $criteria,
                $this->getOrderBy($request),
                ...$this->getOffsetAndLimit($request)
            ),
            $userRepository->countDeletedByCriteria($criteria),
            'users'
        );
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Parameter(name: 'id', description: 'User id', in:'path', schema:  new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when the list of tickets is found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'bool', enum: [false]),
                new OA\Property(property: 'message', type: 'string'),
            ]
        )
    )]
    #[Route(path: '/{id}', name: 'delete_users', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse {
        /** @var CheckUserExistEvent $event */
        $event = $this->dispatchEvent(CheckUserExistEvent::class, $id);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $this->dispatchEvent(DeleteUserEvent::class, $event->getUser());

        $message = $this->translator->trans('flash.success.user_deleted', [], 'application');

        return $this->generateSuccessResponse(message: $message);
    }
}
