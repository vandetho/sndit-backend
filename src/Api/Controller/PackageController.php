<?php
declare(strict_types=1);


namespace App\Api\Controller;


use App\Builder\PackageBuilder;
use App\Constants\EmployeeRole;
use App\DTO\Package;
use App\DTO\PackageHistory;
use App\DTO\Tracking;
use App\Entity\Employee;
use App\Entity\User;
use App\Event\Package\CheckPackageExistEvent;
use App\Event\Template\CreateTemplateEvent;
use App\Form\Types\PackageType;
use App\Model\ErrorResponseData;
use App\Repository\EmployeeRepository;
use App\Repository\PackageHistoryRepository;
use App\Repository\PackageRepository;
use App\Repository\TrackingRepository;
use App\Utils\RequestManipulator;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\PackageWorkflow;
use App\Workflow\Status\PackageStatus;
use App\Workflow\Transition\PackageTransition;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PackageController
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
    content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
)]
#[OA\Tag(name: 'Package')]
#[Route(path: '/packages', name: 'sndit_api_packages_')]
class PackageController extends AbstractController
{
    /**
     * @var PackageRepository
     */
    private PackageRepository $packageRepository;

    /**
     * @var PackageWorkflow
     */
    private PackageWorkflow $packageWorkflow;

    /**
     * PackageController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param PackageRepository          $packageRepository
     * @param PackageWorkflow            $packageWorkflow
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        PackageRepository $packageRepository,
        PackageWorkflow $packageWorkflow
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->packageRepository = $packageRepository;
        $this->packageWorkflow = $packageWorkflow;
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
        description: 'Return when packages that are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', properties: [
                new OA\Property(property: 'totalRows', type: 'integer'),
                new OA\Property(property: 'packages', type: 'array', items: new OA\Items(ref: new Model(type: Package::class))),
            ], type: 'object'),
        ])
    )]
    #[Route(name: 'gets', methods: ['GET'])]
    public function gets(Request $request): JsonResponse
    {
        $criteria = $this->getCriteria([], $request);
        $criteria['user'] = $this->getUser();
        $criteria['role'] = EmployeeRole::ROLE_MANAGER;

        return $this->generateTableData(
            $this->packageRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $this->packageRepository->countByCriteria($criteria),
            'packages'
        );
    }

    /**
     * @param Request            $request
     * @param WorkflowInterface  $packageWorkflow
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: [
            new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'phoneNumber', type: 'string'),
                        new OA\Property(property: 'address', type: 'string'),
                        new OA\Property(property: 'note', type: 'string'),
                        new OA\Property(property: 'company', type: 'integer'),
                        new OA\Property(property: 'city', type: 'integer'),
                        new OA\Property(property: 'images', type: 'string', format: 'binary'),
                    ]
                )
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when the package has been created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])

    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when parameters are missing',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[Route(name: 'post', methods: ['POST'])]
    public function post(Request $request, WorkflowInterface $packageWorkflow, EmployeeRepository $employeeRepository): JsonResponse
    {
        $package = $this->packageRepository->create();
        $form = $this->createForm(PackageType::class, $package);
        $form->submit(RequestManipulator::getData($request, $form));
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $package->getCompany()) {
                $message = $this->translator->trans('flash.errors.company_required', [], 'application');

                return $this->generateErrorResponse($message);
            }
            $package->setToken(Uuid::v4()->__toString());

            /** @var User $user */
            $user = $this->getUser();
            $package->setCreator($user);
            $packageWorkflow->apply($package, PackageTransition::CREATE_NEW_PACKAGE);
            $this->packageRepository->save($package);
            if ($form->get('createTemplate')->getData()) {
                $this->dispatchEvent(CreateTemplateEvent::class, $package);
            }
            $message = $this->translator->trans('flash.success.package_created', [], 'application');
            /** @var Employee $employee */
            $employee = $employeeRepository->findByCompanyAndUser($package->getCompany(), $user);

            return $this->generateSuccessResponse(PackageBuilder::buildDTO($package, $employee), $message, Response::HTTP_CREATED);
        }
        return $this->getMissingParamsResponse();
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
        description: 'Return when packages that are on delivery are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[Route(path: '/deliveries', name: 'deliveries', methods: ['GET'])]
    public function deliveries(Request $request): JsonResponse
    {

        $user = $this->getUser();

        return $this->generateTableData(
            $this->packageRepository->findOnDeliveries($user, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $this->packageRepository->countOnDeliveries($user),
            'packages'
        );
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
        description: 'Return when packages that are on delivery are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[Route(path: '/waiting-for-deliveries', name: 'waiting_for_deliveries', methods: ['GET'])]
    public function waitingForDeliveries(Request $request): JsonResponse
    {

        $user = $this->getUser();

        return $this->generateTableData(
            $this->packageRepository->findWaitingForDeliveries($user, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $this->packageRepository->countWaitingForDeliveries($user),
            'packages'
        );
    }

    /**
     * @param int|string         $idOrToken
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse
     */
    #[OA\Parameter(name: 'idOrToken', description: 'Id or token of package', in: 'path', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when package has been given to deliverer',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[Route(path: '/{idOrToken}', name: 'getc', methods: ['GET'])]
    public function getc(int|string $idOrToken, EmployeeRepository $employeeRepository): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $package = $event->getPackage();

        /** @var Employee $employee */
        $employee = $employeeRepository->findByCompanyAndUser($package->getCompany(), $this->getUser());

        return $this->generateSuccessResponse(PackageBuilder::buildDTO($package, $employee));
    }

    /**
     * @param int|string               $idOrToken
     * @param Request                  $request
     * @param PackageHistoryRepository $packageHistoryRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Id or token of package',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when package histories are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: PackageHistory::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[Route(path: '/{idOrToken}/histories', name: 'histories', methods: ['GET'])]
    public function histories(int|string $idOrToken, Request $request, PackageHistoryRepository $packageHistoryRepository): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $criteria = $this->getCriteria([], $request);
        $criteria['package'] = $event->getPackage();

        return $this->generateTableData(
            $packageHistoryRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $packageHistoryRepository->countByCriteria($criteria),
            'histories'
        );
    }

    /**
     * @param int|string         $idOrToken
     * @param Request            $request
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Id or token of package',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'employee',
                    description: 'Id or token of employee',
                    type: 'string',
                ),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when package has been given to deliverer',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when role is missing',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[Route(path: '/{idOrToken}/give-to-deliverer', name: 'give_to_deliverer', methods: ['POST'])]
    public function giveToDeliverer(int|string $idOrToken, Request $request, EmployeeRepository $employeeRepository): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $content = $request->toArray();
        if (null !== $response = $this->checkParameter($content, 'employee')) {
            return $response;
        }

        $employee = is_numeric($content['employee']) ? $employeeRepository->find($content['employee']) : $employeeRepository->findByToken($content['employee']);
        if (null === $employee) {
            return $this->generateErrorResponse($this->translator->trans('flash.errors.employee_not_found', ['%id%' => $content['employee']], 'application'));
        }

        if ($employee->getUser()?->isDeleting()) {
            $message = $this->translator->trans('flash.errors.user_deleting', [], 'application');
            return $this->generateErrorResponse($message);
        }

        return $this->packageWorkflow->onGiveToDeliverer($event->getPackage(), $employee->getUser());
    }

    /**
     * @param int|string $idOrToken
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Id or token of package',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when package has been delivered',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when role is missing',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[Route(path: '/{idOrToken}/take-package', name: 'take_package', methods: ['POST'])]
    public function takePackage(int|string $idOrToken): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        if ($this->getUser()?->isDeleting()) {
            $message = $this->translator->trans('flash.errors.user_deleting', [], 'application');
            return $this->generateErrorResponse($message);
        }

        return $this->packageWorkflow->onTakePackage($event->getPackage());
    }

    /**
     * @param int|string $idOrToken
     * @param Request    $request
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Id or token of package',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'latitude',
                    description: 'Latitude',
                    type: 'number',
                ),
                new OA\Property(
                    property: 'longitude',
                    description: 'Longitude',
                    type: 'number',
                ),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when package has been delivered',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when role is missing',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[Route(path: '/{idOrToken}/delivered', name: 'delivered', methods: ['POST'])]
    public function delivered(int|string $idOrToken, Request $request): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $content = $request->toArray();
        if (null !== $response = $this->checkParameter($content, 'latitude')) {
            return $response;
        }

        if (null !== $response = $this->checkParameter($content, 'longitude')) {
            return $response;
        }

        return $this->packageWorkflow->onDelivered($event->getPackage(), $content['latitude'], $content['longitude']);
    }

    /**
     * Find deliverer last location
     *
     * @param int|string         $idOrToken
     * @param TrackingRepository $trackingRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Id or token of package',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'latitude',
                    description: 'Latitude',
                    type: 'number',
                ),
                new OA\Property(
                    property: 'longitude',
                    description: 'Longitude',
                    type: 'number',
                ),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when deliverer\'s location has been found',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Tracking::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when package is not in state on delivery',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseData::class))
    )]
    #[Route(path: '/{idOrToken}/locations', name: 'locations', methods: ['GET'])]
    public function delivererLocations(int|string $idOrToken, TrackingRepository $trackingRepository): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $package = $event->getPackage();
        $marking = $package->getMarking();

        if (!isset($marking[PackageStatus::DELIVERED])) {
            $message = $this->translator->trans('flash.errors.package_delivered', [], 'application');

            return $this->generateErrorResponse($message);
        }

        if (!isset($marking[PackageStatus::WAITING_FOR_DELIVERY])) {
            $message = $this->translator->trans('flash.errors.package_delivered', [], 'application');

            return $this->generateErrorResponse($message);
        }

        if (null === $deliverer = $package->getDeliverer()) {
            return $this->generateErrorResponse($this->translator->trans('flash.errors.package_delivered', [], 'application'));
        }

        return $this->generateSuccessResponse($trackingRepository->findLastByUser($deliverer));
    }
}
