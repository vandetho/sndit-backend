<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Entity\Employee;
use App\Entity\Package;
use App\Entity\User;
use App\Event\Package\CheckPackageExistEvent;
use App\Repository\EmployeeRepository;
use App\Repository\PackageRepository;
use App\Repository\UserNotificationTokenRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Utils\SendNotificationInterface;
use App\Workflow\Status\PackageStatus;
use App\Workflow\Transition\PackageTransition;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PackageSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageSubscriber extends AbstractSubscriber
{
    /**
     * @var PackageRepository
     */
    private PackageRepository $packageRepository;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $packageWorkflow;

    /**
     * @var SendNotificationInterface
     */
    private SendNotificationInterface $sendNotification;

    /**
     * @var UserNotificationTokenRepository
     */
    private UserNotificationTokenRepository $notificationTokenRepository;

    /**
     * @var string
     */
    private string $mobileScheme;

    /**
     * PackageSubscriber constructor.
     *
     * @param ResponseGeneratorInterface      $responseGenerator
     * @param TranslatorInterface             $translator
     * @param Security                        $security
     * @param PackageRepository               $packageRepository
     * @param EmployeeRepository              $employeeRepository
     * @param WorkflowInterface               $packageWorkflow
     * @param SendNotificationInterface       $sendNotification
     * @param UserNotificationTokenRepository $notificationTokenRepository
     * @param string                          $mobileScheme
     */
    #[Pure]
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security,
        PackageRepository $packageRepository,
        EmployeeRepository $employeeRepository,
        WorkflowInterface $packageWorkflow,
        SendNotificationInterface $sendNotification,
        UserNotificationTokenRepository $notificationTokenRepository,
        string $mobileScheme
    ) {
        parent::__construct($responseGenerator, $translator, $security);
        $this->packageRepository = $packageRepository;
        $this->employeeRepository = $employeeRepository;
        $this->packageWorkflow = $packageWorkflow;
        $this->sendNotification = $sendNotification;
        $this->notificationTokenRepository = $notificationTokenRepository;
        $this->mobileScheme = $mobileScheme;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CheckPackageExistEvent::class                                                 => 'onCheckExist',
            sprintf('workflow.package.entered.%s', PackageStatus::NOTIFY_DELIVERER)       => 'onNotifyDeliverer',
            sprintf('workflow.package.entered.%s', PackageStatus::DELIVERED_NOTIFICATION) => 'onDeliveredNotification',
            sprintf('workflow.package.guard.%s', PackageTransition::TAKE_PACKAGE)         => ['guardTakePackage'],
            sprintf('workflow.package.guard.%s', PackageTransition::GIVE_TO_DELIVERER)    => ['guardGiveToDeliverer'],
            sprintf('workflow.package.guard.%s', PackageTransition::DELIVER)              => ['guardDeliver'],
        ];
    }

    /**
     * @param CheckPackageExistEvent $event
     * @return void
     */
    public function onCheckExist(CheckPackageExistEvent $event): void
    {
        $idOrToken = $event->getIdOrToken();
        $package = is_numeric($idOrToken) ? $this->packageRepository->find($idOrToken) : $this->packageRepository->findByToken($idOrToken);
        if (null === $package) {
            $message = $this->translator->trans('flash.errors.package_not_found', ['%id%' => $idOrToken], 'application');
            $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_NOT_FOUND));

            return;
        }
        $event->setPackage($package);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onNotifyDeliverer(Event $event): void
    {
        /** @var Package $package */
        $package = $event->getSubject();
        /** @var User $deliverer */
        $deliverer = $package->getDeliverer();
        if (($deliverer !== $this->getUser()) && null !== $token = $this->notificationTokenRepository->findByUser($deliverer)) {
            $body = $this->translator->trans('body.package_given_to_deliverer', [
                '%name%' => $package->getName(),
            ],
                'notification'
            );
            $title = $this->translator->trans('title.package_given_to_deliverer', ['%name%' => $package->getName()], 'notification');
            $this->sendNotification->send([$token], $body, $title, ['url' => $this->mobileScheme.'://packages/'.$package->getToken()]);
        }
        $this->packageWorkflow->apply($package, PackageTransition::DELIVERER_NOTIFIED);
        $this->packageRepository->save($package);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onDeliveredNotification(Event $event): void
    {
        /** @var Package $package */
        $package = $event->getSubject();
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findByCompanyAndUser($package->getCompany(), $user);
        if (!$employee->isOwner()) {
            $users = [];
            if ($employee->isManager()) {
                $owners = $this->employeeRepository->findCompanyOwners($package->getCompany());
                foreach ($owners as $owner) {
                    $users[] = $owner->getUser();
                }
            } else {
                $managers = $this->employeeRepository->findCompanyManagers($package->getCompany());
                foreach ($managers as $manager) {
                    $users[] = $manager->getUser();
                }
            }
            $tokens = $this->notificationTokenRepository->findByUsers($users);
            $body = $this->translator->trans('body.package_delivered', [
                '%name%'     => $package->getName(),
                '%username%' => $user->getFullName(),
            ],
                'notification'
            );
            $title = $this->translator->trans('title.package_delivered', ['%name%' => $package->getName()], 'notification');
            $this->sendNotification->send($tokens, $body, $title, ['url' => $this->mobileScheme.'://packages/'.$package->getToken()]);
        }
        $this->packageWorkflow->apply($package, PackageTransition::DONE);
        $this->packageRepository->save($package);
    }

    /**
     * @param GuardEvent $event
     * @return void
     */
    public function guardGiveToDeliverer(GuardEvent $event): void
    {
        /** @var Package $package */
        $package = $event->getSubject();
        /** @var User $user */
        $user = $this->getUser();
        if (null === $employee = $this->employeeRepository->findByCompanyAndUser($package->getCompany(), $user)) {
            $message = $this->translator->trans('flash.errors.not_employee', [], 'application');
            $event->setBlocked(true, $message);

            return;
        }
        if (!$employee->isManager()) {
            $message = $this->translator->trans('flash.errors.no_permission', [], 'application');
            $event->setBlocked(true, $message);
        }
    }

    /**
     * @param GuardEvent $event
     * @return void
     */
    public function guardDeliver(GuardEvent $event): void
    {
        /** @var Package $package */
        $package = $event->getSubject();
        /** @var User $user */
        $user = $this->getUser();
        if (null === $employee = $this->employeeRepository->findByCompanyAndUser($package->getCompany(), $user)) {
            $message = $this->translator->trans('flash.errors.not_employee', [], 'application');
            $event->setBlocked(true, $message);

            return;
        }
        if (!$employee->isManager() && $package->getDeliverer() !== $employee->getUser()) {
            $message = $this->translator->trans('flash.errors.no_permission', [], 'application');
            $event->setBlocked(true, $message);
        }
    }

    /**
     * @param GuardEvent $event
     * @return void
     */
    public function guardTakePackage(GuardEvent $event): void
    {
        /** @var Package $package */
        $package = $event->getSubject();
        /** @var User $user */
        $user = $this->getUser();
        if (null === $this->employeeRepository->findByCompanyAndUser($package->getCompany(), $user)) {
            $message = $this->translator->trans('flash.errors.not_employee', [], 'application');
            $event->setBlocked(true, $message);
        }
    }
}
