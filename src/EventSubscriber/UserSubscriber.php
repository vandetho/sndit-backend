<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Event\User\CheckUserByIdentifierEvent;
use App\Event\User\CheckUserExistEvent;
use App\Event\User\DeleteUserEvent;
use App\Repository\CompanyRepository;
use App\Repository\EmployeeRepository;
use App\Repository\LastLoginRepository;
use App\Repository\OTPRepository;
use App\Repository\PackageHistoryRepository;
use App\Repository\PackageImageRepository;
use App\Repository\PackageRepository;
use App\Repository\TemplateRepository;
use App\Repository\TrackingRepository;
use App\Repository\UserNotificationMessageRepository;
use App\Repository\UserNotificationTokenRepository;
use App\Repository\UserRepository;
use App\Utils\ResponseGeneratorInterface;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class UserSubscriber extends AbstractSubscriber
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var PackageRepository
     */
    private PackageRepository $packageRepository;

    /**
     * @var PackageHistoryRepository
     */
    private PackageHistoryRepository $packageHistoryRepository;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * @var LastLoginRepository
     */
    private LastLoginRepository $lastLoginRepository;

    /**
     * @var UserNotificationTokenRepository
     */
    private UserNotificationTokenRepository $userNotificationTokenRepository;

    /**
     * @var UserNotificationMessageRepository
     */
    private UserNotificationMessageRepository $userNotificationMessageRepository;

    /**
     * @var TrackingRepository
     */
    private TrackingRepository $trackingRepository;

    /**
     * @var TemplateRepository
     */
    private TemplateRepository $templateRepository;

    /**
     * @var OTPRepository
     */
    private OTPRepository $OTPRepository;

    /**
     * @var PackageImageRepository
     */
    private PackageImageRepository $packageImageRepository;

    /**
     * UserSubscriber constructor.
     *
     * @param ResponseGeneratorInterface        $responseGenerator
     * @param TranslatorInterface               $translator
     * @param Security                          $security
     * @param UserRepository                    $userRepository
     * @param CompanyRepository                 $companyRepository
     * @param PackageRepository                 $packageRepository
     * @param PackageHistoryRepository          $packageHistoryRepository
     * @param EmployeeRepository                $employeeRepository
     * @param LastLoginRepository               $lastLoginRepository
     * @param UserNotificationTokenRepository   $userNotificationTokenRepository
     * @param UserNotificationMessageRepository $userNotificationMessageRepository
     * @param TrackingRepository                $trackingRepository
     * @param TemplateRepository                $templateRepository
     * @param OTPRepository                     $OTPRepository
     * @param PackageImageRepository            $packageImageRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        PackageRepository $packageRepository,
        PackageHistoryRepository $packageHistoryRepository,
        EmployeeRepository $employeeRepository,
        LastLoginRepository $lastLoginRepository,
        UserNotificationTokenRepository $userNotificationTokenRepository,
        UserNotificationMessageRepository $userNotificationMessageRepository,
        TrackingRepository $trackingRepository,
        TemplateRepository $templateRepository,
        OTPRepository $OTPRepository,
        PackageImageRepository $packageImageRepository
    ) {
        parent::__construct($responseGenerator, $translator, $security);
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->packageRepository = $packageRepository;
        $this->packageHistoryRepository = $packageHistoryRepository;
        $this->employeeRepository = $employeeRepository;
        $this->lastLoginRepository = $lastLoginRepository;
        $this->userNotificationTokenRepository = $userNotificationTokenRepository;
        $this->userNotificationMessageRepository = $userNotificationMessageRepository;
        $this->trackingRepository = $trackingRepository;
        $this->templateRepository = $templateRepository;
        $this->OTPRepository = $OTPRepository;
        $this->packageImageRepository = $packageImageRepository;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        CheckUserExistEvent::class        => "string",
        CheckUserByIdentifierEvent::class => "string",
        DeleteUserEvent::class            => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            CheckUserExistEvent::class        => 'onCheckExist',
            CheckUserByIdentifierEvent::class => 'onCheckByIdentifier',
            DeleteUserEvent::class            => 'onDeleteUser',
        ];
    }

    /**
     * This event is called to check if a stores exist or not
     *
     * @param CheckUserExistEvent $event
     */
    public function onCheckExist(CheckUserExistEvent $event): void
    {
        $idOrToken = $event->getIdOrToken();
        $user = is_numeric($idOrToken) ? $this->userRepository->find($idOrToken) : $this->userRepository->findByToken($idOrToken);
        if (null === $user) {
            $message = $this->translator->trans(
                'flash.errors.user_not_found',
                ['%id%' => $idOrToken],
                'application'
            );
            $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_NOT_FOUND));

            return;
        }
        $event->setUser($user);
    }

    /**
     * @param CheckUserByIdentifierEvent $event
     * @return void
     * @throws NonUniqueResultException
     */
    public function onCheckByIdentifier(CheckUserByIdentifierEvent $event): void
    {
        if (null === $event->getIdentifier()) {
            $message = $this->translator->trans('flash.errors.identifier_missing', [], 'application');
            $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_UNAUTHORIZED));

            return;
        }

        if (null === $user = $this->userRepository->findByIdentifier($event->getIdentifier())) {
            $message = $this->translator->trans('flash.errors.identifier_not_found', ['%identifier%' => $event->getIdentifier()], 'application');
            $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_UNAUTHORIZED));

            return;
        }

        $event->setUser($user);
    }

    /**
     * @param DeleteUserEvent $event
     * @return void
     */
    public function onDeleteUser(DeleteUserEvent $event): void
    {
        $user = $event->getUser();

        $templates = $this->templateRepository->findBy(['creator' => $user]);
        $this->deleteEntities($templates);

        $lastLogins = $this->lastLoginRepository->findBy(['user' => $user]);
        $this->deleteEntities($lastLogins);

        $otps = $this->OTPRepository->findBy(['user' => $user]);
        $this->deleteEntities($otps);

        $messages = $this->userNotificationMessageRepository->findBy(['user' => $user]);
        $this->deleteEntities($messages);

        $tokens = $this->userNotificationTokenRepository->findBy(['user' => $user]);
        $this->deleteEntities($tokens);

        $tracking = $this->trackingRepository->findBy(['user' => $user]);
        $this->deleteEntities($tracking);

        $companies = $this->companyRepository->findByOwner($user);
        if (count($companies) > 0) {
            $packages = $this->packageRepository->findByCompanies($companies);
            if (count($packages) > 0) {
                $histories = $this->packageHistoryRepository->findByPackages($packages);
                $this->deleteEntities($histories);

                $images = $this->packageImageRepository->findByPackages($packages);
                $this->deleteEntities($images);

                $this->deleteEntities($packages);

            }
            $employees = $this->employeeRepository->findBy(['company' => $companies]);
            $this->deleteEntities($employees);

            $templates = $this->templateRepository->findBy(['company' => $companies]);
            $this->deleteEntities($templates);

            $this->deleteEntities($companies);
        }

        $packages = $this->packageRepository->findBy(['deliverer' => $user]);
        if (count($packages) > 0) {
            foreach ($packages as $index => $package) {
                $package->setDeliverer(null);
                $this->packageRepository->update($package, false);
                if ($index % 20 === 0) {
                    $this->packageRepository->flush();
                }
            }
            $this->packageRepository->flush();

            $histories = $this->packageHistoryRepository->findByPackages($packages);
            foreach ($histories as $index => $history) {
                $history->setUser(null);
                $this->packageHistoryRepository->update($history, false);
                if ($index % 20 === 0) {
                    $this->packageHistoryRepository->flush();
                }
            }
            $this->packageHistoryRepository->flush();
        }

        $this->userRepository->delete($user);
    }

    /**
     * @param array $entities
     * @return void
     */
    private function deleteEntities(array $entities): void
    {
        foreach ($entities as $index => $entity) {
            $this->userRepository->delete($entity, false);
            if ($index % 20 === 0) {
                $this->userRepository->flush();
            }
        }
        $this->userRepository->flush();
    }
}
