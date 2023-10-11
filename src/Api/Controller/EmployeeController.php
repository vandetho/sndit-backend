<?php
declare(strict_types=1);


namespace App\Api\Controller;

use App\Builder\EmployeeBuilder;
use App\DTO\Employee as EmployeeDTO;
use App\DTO\Tracking;
use App\Event\Employee\CheckEmployeeExistEvent;
use App\Event\Employee\HasOnDeliveryPackageEvent;
use App\Model\ErrorResponseData;
use App\Repository\EmployeeRepository;
use App\Repository\TrackingRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\EmployeeWorkflow;
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
 * Class EmployeeController
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
    examples: [
        new OA\Examples(
            example: 'Bearer XXXXXXXXXXXXXXXXX',
            summary: 'Example of the JWT token'
        ),
    ]
)]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Return when user is not fully authenticated',
    content: new Model(type: ErrorResponseData::class)
)]
#[OA\Tag(name: 'Employee')]
#[Route(path: "/employees", name: "sndit_api_employees_", priority: 2)]
class EmployeeController extends AbstractController
{
    /**
     * @var EmployeeWorkflow
     */
    private EmployeeWorkflow $employeeWorkflow;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * EmployeeController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param EmployeeWorkflow           $employeeWorkflow
     * @param EmployeeRepository         $employeeRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        EmployeeWorkflow $employeeWorkflow,
        EmployeeRepository $employeeRepository
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->employeeWorkflow = $employeeWorkflow;
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
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
        response: Response::HTTP_OK,
        description: 'Return when the list of employees is found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'bool', enum: [false]),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'data', properties: [
                    new OA\Property(
                        property: 'employees',
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: EmployeeDTO::class))
                    ),
                    new OA\Property(property: 'totalRows', type: 'integer'),
                ], type: 'object'),
            ]
        )
    )]
    #[Route(name: 'gets', methods: ['GET'])]
    public function gets(Request $request): JsonResponse
    {
        $criteria = $this->getCriteria([], $request);

        $criteria['user'] = $this->getUser();

        return $this->generateTableData(
            $this->employeeRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $this->employeeRepository->countByCriteria($criteria),
            'employees'
        );
    }

    /**
     * @param string|int $idOrToken
     * @return JsonResponse|null
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Employee Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when employee is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when user is not presented',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when employee is retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: EmployeeDTO::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}', name: 'getc_employees', methods: ['GET'])]
    public function getc(string|int $idOrToken): ?JsonResponse
    {
        /** @var CheckEmployeeExistEvent $event */
        $event = $this->dispatchEvent(CheckEmployeeExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }


        return $this->generateSuccessResponse(EmployeeBuilder::buildDTO($event->getEmployee()));
    }

    /**
     * @param int|string $idOrToken
     * @param Request    $request
     * @return JsonResponse|null
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Id or token of employee',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        ))]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'role',
                    description: 'Employee new role',
                    type: 'string',
                ),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when employee role is changed',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when employee is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when role is missing',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(path: '/{idOrToken}/change-roles', name: 'change_roles', methods: ['POST'])]
    public function changeRoles(int|string $idOrToken, Request $request): ?JsonResponse
    {
        /** @var CheckEmployeeExistEvent $event */
        $event = $this->dispatchEvent(CheckEmployeeExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $content = $request->toArray();
        if (null !== $response = $this->checkParameter($content, 'role')) {
            return $response;
        }

        return $this->employeeWorkflow->onChangeRole($event->getEmployee(), $content['role']);
    }

    /**
     * @param int|string $idOrToken
     * @param Request    $request
     * @return JsonResponse|null
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Employee Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'status',
                    description: 'Employee status',
                    type: 'string',
                ),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when employee status is changed',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when employee status is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when role is missing',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(path: '/{idOrToken}/status', name: 'status', methods: ['POST'])]
    public function status(int|string $idOrToken, Request $request): ?JsonResponse
    {
        /** @var CheckEmployeeExistEvent $event */
        $event = $this->dispatchEvent(CheckEmployeeExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $content = $request->toArray();
        if (null !== $response = $this->checkParameter($content, 'status')) {
            return $response;
        }

        return $this->employeeWorkflow->onChangeStatus($event->getEmployee(), $content['status']);
    }

    /**
     * @param int|string         $idOrToken
     * @param TrackingRepository $trackingRepository
     * @return JsonResponse|null
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Employee Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when team user role is changed',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Tracking::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when employee is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when employee does not have any packages on delivery',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(path: '/{idOrToken}/locations', name: 'locations', methods: ['GET'])]
    public function locations(int|string $idOrToken, TrackingRepository $trackingRepository): ?JsonResponse
    {
        /** @var CheckEmployeeExistEvent $event */
        $event = $this->dispatchEvent(CheckEmployeeExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $employee = $event->getEmployee();
        $event = $this->dispatchEvent(HasOnDeliveryPackageEvent::class, $employee);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return $this->generateSuccessResponse($trackingRepository->findLastByUser($employee->getUser()));
    }
}
