<?php
declare(strict_types=1);


namespace App\Api\Controller;


use App\Builder\CompanyBuilder;
use App\Constants\EmployeeRole;
use App\DTO\Company;
use App\Entity\Employee;
use App\Entity\User;
use App\Event\Company\CheckCompanyExistEvent;
use App\Event\Company\CheckCompanyUniqueNameEvent;
use App\Event\User\CheckUserExistEvent;
use App\Form\Types\CompanyType;
use App\Model\ErrorResponseData;
use App\Repository\CompanyRepository;
use App\Repository\EmployeeRepository;
use App\Repository\PackageRepository;
use App\Utils\RequestManipulator;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\CompanyWorkflow;
use App\Workflow\Transition\CompanyTransition;
use App\Workflow\Transition\EmployeeTransition;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CompanyController
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
#[OA\Tag(name: 'Company')]
#[Route(path: "/companies", name: "sndit_api_companies_")]
class CompanyController extends AbstractController
{
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var CompanyWorkflow
     */
    private CompanyWorkflow $companyWorkflow;

    /**
     * CompanyController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param CompanyRepository          $companyRepository
     * @param CompanyWorkflow            $companyWorkflow
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        CompanyRepository $companyRepository,
        CompanyWorkflow $companyWorkflow
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->companyRepository = $companyRepository;
        $this->companyWorkflow = $companyWorkflow;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when companies are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', properties: [
                new OA\Property(property: 'companies', type: 'array', items: new OA\Items(ref: new Model(type: Company::class))),
                new OA\Property(property: 'totalRows', type: 'integer'),
            ], type: 'object'),
        ])
    )]
    #[Route(name: 'gets', methods: ['GET'])]
    public function gets(Request $request): JsonResponse
    {
        $criteria = $this->getCriteria([], $request);
        $criteria['user'] = $this->getUser();

        return $this->generateTableData(
            $this->companyRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $this->companyRepository->countByCriteria($criteria),
            'companies');
    }

    /**
     * @param Request                $request
     * @param WorkflowInterface      $employeeWorkflow
     * @param WorkflowInterface      $companyWorkflow
     * @param RoleHierarchyInterface $roleHierarchy
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when company is created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Company::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when parameters are missing',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(name: 'post', methods: ['POST'])]
    public function post(Request $request, WorkflowInterface $employeeWorkflow, WorkflowInterface $companyWorkflow, RoleHierarchyInterface $roleHierarchy): JsonResponse
    {
        $company = $this->companyRepository->create();
        $form = $this->createForm(CompanyType::class, $company);
        $form->submit(RequestManipulator::getData($request, $form));
        if ($form->isSubmitted() && $form->isValid()) {
            $company->setCanonicalName(strtolower($company->getName()));
            $event = $this->dispatchEvent(CheckCompanyUniqueNameEvent::class, $company);
            if ($event->getResponse()) {
                return $event->getResponse();
            }
            $user = $this->getUser();
            $company->setUser($user);
            $company->setToken(Uuid::v4()->__toString());
            $employee = new Employee();
            $employee->setUser($user);
            $employee->setCompany($company);
            $employee->setCreator($user);
            $employee->setToken(Uuid::v4()->__toString());
            $employee->setRoles($roleHierarchy->getReachableRoleNames([EmployeeRole::ROLE_OWNER]));
            $employeeWorkflow->apply($employee, EmployeeTransition::CREATE_NEW_EMPLOYEE);
            $this->companyRepository->save($employee, false);
            $companyWorkflow->apply($company, CompanyTransition::CREATE_COMPANY);
            $this->companyRepository->save($company);

            $message = $this->translator->trans('flash.success.company_created', ['%name%' => $company->getName()], 'application');

            return $this->generateSuccessResponse(CompanyBuilder::buildDTO($company, $employee), $message, Response::HTTP_CREATED);
        }

        return $this->getMissingParamsResponse();
    }

    /**
     * @param string|int         $idOrToken
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse|null
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Company Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when company is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when product is created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Company::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}', name: 'getc', methods: ['GET'])]
    public function getc(string|int $idOrToken, EmployeeRepository $employeeRepository): ?JsonResponse
    {
        /** @var CheckCompanyExistEvent $event */
        $event = $this->dispatchEvent(CheckCompanyExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $employee = $employeeRepository->findByCompanyAndUser($event->getCompany(), $this->getUser());

        return $this->generateSuccessResponse(CompanyBuilder::buildDTO($event->getCompany(), $employee));
    }

    /**
     * @param string|int $idOrToken
     * @param Request    $request
     * @return JsonResponse|null
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Company Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'user', description: 'User Id or Token', type: 'string'),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when company or user is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when user is not presented',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when employee is added',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: \App\DTO\Employee::class)),
        ])
    )]
    #[Route(path: '/{idOrToken}/employees', name: 'post_employees', methods: ['POST'])]
    public function employees(string|int $idOrToken, Request $request): ?JsonResponse
    {
        /** @var CheckCompanyExistEvent $event */
        $event = $this->dispatchEvent(CheckCompanyExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $content = $request->toArray();
        if (null !== $response = $this->checkParameter($content, 'user')) {
            return $response;
        }

        /** @var CheckUserExistEvent $userEvent */
        $userEvent = $this->dispatchEvent(CheckUserExistEvent::class, $content['user']);
        if ($userEvent->getResponse()) {
            return $userEvent->getResponse();
        }

        return $this->companyWorkflow->onAddEmployee($event->getCompany(), $userEvent->getUser());
    }

    /**
     * @param string|int         $idOrToken
     * @param Request            $request
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse|null
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Company Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'user', description: 'User Id or Token', type: 'string'),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when company or user is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when user is not presented',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when companies are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', properties: [
                new OA\Property(property: 'employees', type: 'array', items: new OA\Items(ref: new Model(type: \App\DTO\Employee::class))),
                new OA\Property(property: 'totalRows', type: 'integer'),
            ], type: 'object'),
        ])
    )]
    #[Route(path: '/{idOrToken}/employees', name: 'getc_employees', methods: ['GET'])]
    public function getcEmployees(string|int $idOrToken, Request $request, EmployeeRepository $employeeRepository): ?JsonResponse
    {
        /** @var CheckCompanyExistEvent $event */
        $event = $this->dispatchEvent(CheckCompanyExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $criteria = $this->getCriteria([], $request);
        $criteria['company'] = $event->getCompany();

        return $this->generateTableData(
            $employeeRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $employeeRepository->countByCriteria($criteria),
            'employees'
        );
    }

    /**
     * @param string|int        $idOrToken
     * @param Request           $request
     * @param PackageRepository $packageRepository
     * @return JsonResponse|null
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Company Id or Token',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'user', description: 'User Id or Token', type: 'string'),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when company or user is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when user is not presented',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when companies are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', properties: [
                new OA\Property(property: 'employees', type: 'array', items: new OA\Items(ref: new Model(type: \App\DTO\Employee::class))),
                new OA\Property(property: 'totalRows', type: 'integer'),
            ], type: 'object'),
        ])
    )]
    #[Route(path: '/{idOrToken}/packages', name: 'getc_packages', methods: ['GET'])]
    public function packages(string|int $idOrToken, Request $request, PackageRepository $packageRepository): ?JsonResponse
    {
        /** @var CheckCompanyExistEvent $event */
        $event = $this->dispatchEvent(CheckCompanyExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $company = $event->getCompany();

        /** @var User $user */
        $user = $this->getUser();

        return $this->generateTableData(
            $packageRepository->findByCompanyAndUser($company, $user, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $packageRepository->countByCompanyAndUser($company, $user),
            'packages'
        );
    }
}
