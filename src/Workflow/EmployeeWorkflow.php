<?php
declare(strict_types=1);


namespace App\Workflow;


use App\Builder\EmployeeBuilder;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\Status\EmployeeStatus;
use App\Workflow\Transition\EmployeeTransition;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmployeeWorkflow
 *
 * @package App\Workflow
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class EmployeeWorkflow extends AbstractWorkflow
{
    /**
     * @var RoleHierarchyInterface
     */
    private RoleHierarchyInterface $roleHierarchy;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $employeeWorkflow;

    /**
     * EmployeeWorkflow constructor.
     *
     * @param Security                   $security
     * @param TranslatorInterface        $translator
     * @param ResponseGeneratorInterface $responseGenerator
     * @param RoleHierarchyInterface     $roleHierarchy
     * @param EmployeeRepository    $employeeRepository
     * @param WorkflowInterface          $employeeWorkflow
     */
    #[Pure]
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        ResponseGeneratorInterface $responseGenerator,
        RoleHierarchyInterface $roleHierarchy,
        EmployeeRepository $employeeRepository,
        WorkflowInterface $employeeWorkflow
    ) {
        parent::__construct($security, $translator, $responseGenerator);
        $this->roleHierarchy = $roleHierarchy;
        $this->employeeRepository = $employeeRepository;
        $this->employeeWorkflow = $employeeWorkflow;
    }

    /**
     * @param Employee $employee
     * @param string            $role
     * @return JsonResponse
     */
    public function onChangeRole(Employee $employee, string $role): JsonResponse
    {
        $this->employeeWorkflow->apply($employee, EmployeeTransition::CHANGE_ROLE);
        $employee->setRoles($this->roleHierarchy->getReachableRoleNames([$role]));
        $this->employeeWorkflow->apply($employee, EmployeeTransition::ROLE_CHANGED);
        $this->employeeRepository->flush();

        $message = $this->translator->trans(
            'flash.success.employee_role_updated',
            [],
            'application'
        );

        return $this->responseGenerator->generateSuccess(EmployeeBuilder::buildDTO($employee), $message, Response::HTTP_CREATED);
    }

    /**
     * @param Employee $employee
     * @param string   $status
     * @return JsonResponse
     */
    public function onChangeStatus(Employee $employee, string $status): JsonResponse
    {
        $transition = $status === EmployeeStatus::ACTIVE ? EmployeeTransition::REACTIVATE : EmployeeTransition::DEACTIVATE;
        if ($this->employeeWorkflow->can($employee, $transition)) {
            $this->employeeWorkflow->apply($employee, $transition);
            $this->employeeRepository->flush();

            $message = $this->translator->trans(
                'flash.success.employee_role_updated',
                [],
                'application'
            );

            return $this->responseGenerator->generateSuccess(EmployeeBuilder::buildDTO($employee), $message, Response::HTTP_CREATED);
        }
        $message = $this->translator->trans(
            'flash.errors.employee_status_incorrect',
            [],
            'application'
        );
        return $this->responseGenerator->generateError($message);

    }
}
