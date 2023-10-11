<?php
declare(strict_types=1);


namespace App\EventSubscriber;

use App\Constants\EmployeeRole;
use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\User;
use App\Event\Company\CheckCompanyExistEvent;
use App\Event\Company\CheckCompanyUniqueNameEvent;
use App\Repository\CompanyRepository;
use App\Repository\EmployeeRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\Status\CompanyStatus;
use App\Workflow\Transition\CompanyTransition;
use App\Workflow\Transition\EmployeeTransition;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CompanySubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CompanySubscriber extends AbstractSubscriber
{
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $companyWorkflow;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $employeeWorkflow;

    /**
     * @var RoleHierarchyInterface
     */
    private RoleHierarchyInterface $roleHierarchy;

    /**
     * CompanySubscriber constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param Security                   $security
     * @param CompanyRepository          $companyRepository
     * @param EmployeeRepository         $employeeRepository
     * @param WorkflowInterface          $companyWorkflow
     * @param WorkflowInterface          $employeeWorkflow
     * @param RoleHierarchyInterface     $roleHierarchy
     */
    #[Pure]
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security,
        CompanyRepository $companyRepository,
        EmployeeRepository $employeeRepository,
        WorkflowInterface $companyWorkflow,
        WorkflowInterface $employeeWorkflow,
        RoleHierarchyInterface $roleHierarchy
    ) {
        parent::__construct($responseGenerator, $translator, $security);
        $this->companyRepository = $companyRepository;
        $this->employeeRepository = $employeeRepository;
        $this->companyWorkflow = $companyWorkflow;
        $this->employeeWorkflow = $employeeWorkflow;
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        CheckCompanyExistEvent::class      => "string",
        CheckCompanyUniqueNameEvent::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            CheckCompanyExistEvent::class                                          => 'onCheckExist',
            CheckCompanyUniqueNameEvent::class                                     => 'onCheckUniqueName',
            sprintf('workflow.company.guard.%s', CompanyTransition::ADD_EMPLOYEE)  => ['guardAddEmployee'],
            sprintf('workflow.company.entered.%s', CompanyStatus::ADDING_EMPLOYEE) => 'onAddingEmployee',
        ];
    }

    /**
     * @param CheckCompanyExistEvent $event
     * @return void
     */
    public function onCheckExist(CheckCompanyExistEvent $event): void
    {
        $idOrToken = $event->getIdOrToken();
        $company = is_numeric($idOrToken) ? $this->companyRepository->find($idOrToken) : $this->companyRepository->findByToken($idOrToken);
        if (null === $company) {
            $message = $this->translator->trans('flash.errors.company_not_found', ['%id%' => $idOrToken], 'application');
            $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_NOT_FOUND));

            return;
        }
        $event->setCompany($company);
    }

    /**
     * @param CheckCompanyUniqueNameEvent $event
     * @return void
     */
    public function onCheckUniqueName(CheckCompanyUniqueNameEvent $event): void
    {
        $name = $event->getCompany()->getName();
        $canonicalName = $event->getCompany()->getCanonicalName();
        if ((null !== $company = $this->companyRepository->findOneBy([
                    'canonicalName' => $canonicalName])
            ) && $company->getId() !== $event->getCompany()->getId()) {
            $message = $this->translator->trans('flash.errors.company_name_exist', ['%name%' => $name], 'application');
            $event->setResponse($this->responseGenerator->generateError($message));
        }
    }

    /**
     * @param GuardEvent $event
     * @return void
     */
    public function guardAddEmployee(GuardEvent $event): void
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Company $subject */
        $subject = $event->getSubject();
        if (null === $employee = $this->employeeRepository->findByCompanyAndUser($subject, $user)) {
            $message = $this->translator->trans('flash.errors.no_permission', [], 'application');
            $event->setBlocked(true, $message);

            return;
        }
        if (!$employee->isManager()) {
            $message = $this->translator->trans('flash.errors.no_permission', [], 'application');
            $event->setBlocked(true, $message);
        }
        if (!$employee->isManager()) {
            $message = $this->translator->trans('flash.errors.no_permission', [], 'application');
            $event->setBlocked(true, $message);
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onAddingEmployee(Event $event): void
    {
        /** @var Company $company */
        $company = $event->getSubject();
        $user = $event->getContext()['user'];
        $employee = new Employee();
        $employee->setCompany($company);
        $employee->setUser($user);
        $employee->setCreator($this->getUser());
        $employee->setToken(Uuid::v4()->__toString());
        $employee->setRoles($this->roleHierarchy->getReachableRoleNames([EmployeeRole::ROLE_EMPLOYEE]));
        $this->companyWorkflow->apply($company, CompanyTransition::EMPLOYEE_ADDED);
        $this->employeeWorkflow->apply($employee, EmployeeTransition::CREATE_NEW_EMPLOYEE);
        $this->companyRepository->save($employee);
    }
}
