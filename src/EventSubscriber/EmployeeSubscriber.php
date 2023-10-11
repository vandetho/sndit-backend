<?php
declare(strict_types=1);


namespace App\EventSubscriber;

use App\Entity\Employee;
use App\Entity\User;
use App\Event\Employee\CheckEmployeeExistEvent;
use App\Event\Employee\HasOnDeliveryPackageEvent;
use App\Repository\EmployeeRepository;
use App\Repository\PackageRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\Transition\EmployeeTransition;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmployeeSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class EmployeeSubscriber extends AbstractSubscriber
{
    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * @var PackageRepository
     */
    private PackageRepository $packageRepository;

    /**
     * EmployeeSubscriber constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param Security                   $security
     * @param EmployeeRepository         $employeeRepository
     * @param PackageRepository          $packageRepository
     */
    #[Pure]
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security,
        EmployeeRepository $employeeRepository,
        PackageRepository $packageRepository
    ) {
        parent::__construct($responseGenerator, $translator, $security);
        $this->employeeRepository = $employeeRepository;
        $this->packageRepository = $packageRepository;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        CheckEmployeeExistEvent::class   => "string",
        HasOnDeliveryPackageEvent::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            CheckEmployeeExistEvent::class                                         => 'onCheckExist',
            HasOnDeliveryPackageEvent::class                                       => 'onCheckHasOnDeliveryPackages',
            sprintf('workflow.employee.guard.%s', EmployeeTransition::CHANGE_ROLE) => ['guardEmployeeRoleChange'],
        ];
    }

    /**
     * @param CheckEmployeeExistEvent $event
     * @return void
     */
    public function onCheckExist(CheckEmployeeExistEvent $event): void
    {
        $idOrToken = $event->getIdOrToken();
        $employee = is_numeric($idOrToken) ? $this->employeeRepository->find($idOrToken) : $this->employeeRepository->findByToken($idOrToken);
        if (null === $employee) {
            $message = $this->translator->trans('flash.errors.employee_not_found', ['%id%' => $idOrToken], 'application');
            $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_NOT_FOUND));

            return;
        }
        $event->setEmployee($employee);
    }

    /**
     * @param CheckEmployeeExistEvent $event
     * @return void
     * @throws NonUniqueResultException
     */
    public function onCheckHasOnDeliveryPackages(CheckEmployeeExistEvent $event): void
    {
        $employee = $event->getEmployee();

        if ($this->packageRepository->employeeHasOnDeliveries($employee)) {
            return;
        }

        $message = $this->translator->trans('flash.errors.employee_no_package_on_delivery', [], 'application');
        $event->setResponse($this->responseGenerator->generateError($message));
    }

    /**
     * @param GuardEvent $event
     * @return void
     */
    public function guardEmployeeRoleChange(GuardEvent $event): void
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $employee */
        $employee = $event->getSubject();
        if (null === $employee = $this->employeeRepository->findByCompanyAndUser($employee->getCompany(), $user)) {
            $message = $this->translator->trans('flash.errors.no_permission', [], 'application');
            $event->setBlocked(true, $message);

            return;
        }
        if (!$employee->isManager()) {
            $message = $this->translator->trans('flash.errors.no_permission', [], 'application');
            $event->setBlocked(true, $message);
        }
    }
}
