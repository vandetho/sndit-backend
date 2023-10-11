<?php
declare(strict_types=1);


namespace App\Workflow;


use App\Builder\PackageBuilder;
use App\Entity\Employee;
use App\Entity\Package;
use App\Entity\User;
use App\Repository\EmployeeRepository;
use App\Repository\PackageRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\Transition\PackageTransition;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PackageWorkflow
 *
 * @package App\Workflow
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageWorkflow extends AbstractWorkflow
{
    /**
     * @var PackageRepository
     */
    private PackageRepository $packageRepository;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $packageWorkflow;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * PackageWorkflow constructor.
     *
     * @param Security                   $security
     * @param TranslatorInterface        $translator
     * @param ResponseGeneratorInterface $responseGenerator
     * @param PackageRepository          $packageRepository
     * @param WorkflowInterface          $packageWorkflow
     * @param EmployeeRepository         $employeeRepository
     */
    #[Pure]
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        ResponseGeneratorInterface $responseGenerator,
        PackageRepository $packageRepository,
        WorkflowInterface $packageWorkflow,
        EmployeeRepository $employeeRepository
    ) {
        parent::__construct($security, $translator, $responseGenerator);
        $this->packageRepository = $packageRepository;
        $this->packageWorkflow = $packageWorkflow;
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * @param Package $package
     * @param User    $deliverer
     * @return JsonResponse
     */
    public function onGiveToDeliverer(Package $package, User $deliverer): JsonResponse
    {
        $package->setDeliverer($deliverer);
        $this->packageWorkflow->apply($package, PackageTransition::GIVE_TO_DELIVERER);
        $this->packageRepository->update($package);
        $message = $this->translator->trans('flash.success.package_given_to_deliverer', [], 'application');

        /** @var Employee $employee */
        $employee = $this->employeeRepository->findByCompanyAndUser($package->getCompany(), $this->getUser());

        return $this->responseGenerator->generateSuccess(PackageBuilder::buildDTO($package, $employee), message: $message);
    }

    /**
     * @param Package $package
     * @return JsonResponse
     */
    public function onTakePackage(Package $package): JsonResponse
    {
        $package->setDeliverer($this->getUser());
        $this->packageWorkflow->apply($package, PackageTransition::TAKE_PACKAGE);
        $this->packageRepository->update($package);
        $message = $this->translator->trans('flash.success.package_taken', [], 'application');
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findByCompanyAndUser($package->getCompany(), $this->getUser());

        return $this->responseGenerator->generateSuccess(PackageBuilder::buildDTO($package, $employee), message: $message);
    }

    /**
     * @param Package    $package
     * @param float|null $latitude
     * @param float|null $longitude
     * @return JsonResponse
     */
    public function onDelivered(Package $package, ?float $latitude, ?float $longitude): JsonResponse
    {
        $this->packageWorkflow->apply($package, PackageTransition::DELIVER);
        $package->setLatitude($latitude);
        $package->setLongitude($longitude);
        $this->packageRepository->update($package);
        $message = $this->translator->trans('flash.success.package_delivered', [], 'application');
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findByCompanyAndUser($package->getCompany(), $this->getUser());

        return $this->responseGenerator->generateSuccess(PackageBuilder::buildDTO($package, $employee), message: $message);
    }
}
