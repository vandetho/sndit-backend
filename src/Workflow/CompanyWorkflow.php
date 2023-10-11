<?php
declare(strict_types=1);


namespace App\Workflow;


use App\Entity\Company;
use App\Entity\User;
use App\Repository\EmployeeRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\Transition\CompanyTransition;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CompanyWorkflow
 *
 * @package App\Workflow
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CompanyWorkflow extends AbstractWorkflow
{
    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $companyWorkflow;

    private EmployeeRepository $employeeRepository;

    /**
     * CompanyWorkflow constructor.
     *
     * @param Security                   $security
     * @param TranslatorInterface        $translator
     * @param ResponseGeneratorInterface $responseGenerator
     * @param WorkflowInterface          $companyWorkflow
     * @param EmployeeRepository         $employeeRepository
     */
    #[Pure]
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        ResponseGeneratorInterface $responseGenerator,
        WorkflowInterface $companyWorkflow,
        EmployeeRepository $employeeRepository,
    ) {
        parent::__construct($security, $translator, $responseGenerator);
        $this->companyWorkflow = $companyWorkflow;
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * @param Company $company
     * @param User  $user
     * @return JsonResponse|null
     */
    public function onAddEmployee(Company $company, User $user): ?JsonResponse
    {
        if (null === $this->employeeRepository->findByCompanyAndUser($company, $user)) {
            $this->companyWorkflow->apply($company, CompanyTransition::ADD_EMPLOYEE, ['user' => $user]);
            $message = $this->translator->trans('flash.success.employee_added', [], 'application');
            return $this->responseGenerator->generateSuccess(null, $message, Response::HTTP_CREATED);
        }
        $message = $this->translator->trans('flash.errors.employee_already_exists', [], 'application');
        return $this->responseGenerator->generateError($message);
    }
}
