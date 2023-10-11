<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Entity\Package;
use App\Entity\PackageHistory;
use App\Entity\User;
use App\Repository\PackageHistoryRepository;
use App\Utils\ResponseGeneratorInterface;
use App\Workflow\Transition\PackageTransition;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PackageHistorySubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageHistorySubscriber extends AbstractSubscriber
{
    /**
     * @var PackageHistoryRepository
     */
    private PackageHistoryRepository $packageHistoryRepository;

    private const ACTIONS = [
        PackageTransition::CREATE_NEW_PACKAGE => 'onCreate',
        PackageTransition::DELIVER            => 'onDeliver',
        PackageTransition::TAKE_PACKAGE       => 'onGiveToDeliverer',
        PackageTransition::GIVE_TO_DELIVERER  => 'onGiveToDeliverer',
        PackageTransition::DELIVERER_NOTIFIED => 'onNotifyDeliverer',
        PackageTransition::DONE               => 'onDone',
    ];

    /**
     * PackageHistorySubscriber constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param Security                   $security
     * @param PackageHistoryRepository   $packageHistoryRepository
     */
    #[Pure]
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security,
        PackageHistoryRepository $packageHistoryRepository,
    ) {
        parent::__construct($responseGenerator, $translator, $security);
        $this->packageHistoryRepository = $packageHistoryRepository;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['workflow.package.leave' => "onLeave"])]
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.package.leave' => 'onLeave',
        ];
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onLeave(Event $event): void
    {
        /** @var Package $package */
        $package = $event->getSubject();
        $history = new PackageHistory();
        $history->setUser($this->getUser());
        $history->setPackage($package);
        $history->setTransitionName($event->getTransition()?->getName());
        $action = self::ACTIONS[$history->getTransitionName()];
        $description = $this->$action($package);
        $history->setDescription(trim($description));
        $this->packageHistoryRepository->save($history, false);
    }

    /**
     * @return string
     */
    private function onCreate(): string
    {
        /** @var User $user */
        $user = $this->getUser();
        $fullName = "{$user->getLastName()} {$user->getFirstName()}";

        return <<<EOL
            Package has been created by $fullName.
        EOL;
    }

    /**
     * @return string
     */
    private function onDeliver(): string
    {
        /** @var User $user */
        $user = $this->getUser();
        $fullName = "{$user->getLastName()} {$user->getFirstName()}";

        return <<<EOL
            Package has been delivered by $fullName.
        EOL;
    }

    /**
     * @param Package $package
     * @return string
     */
    private function onGiveToDeliverer(Package $package): string
    {
        /** @var User $user */
        $user = $this->getUser();
        $fullName = "{$user->getLastName()} {$user->getFirstName()}";
        /** @var User $deliverer */
        $deliverer = $package->getDeliverer();

        return <<<EOL
            Package has been given to {$deliverer->getLastName()} {$deliverer->getFirstName()} for delivery by $fullName.
        EOL;
    }

    /**
     * @param Package $package
     * @return string
     */
    private function onNotifyDeliverer(Package $package): string
    {
        /** @var User $user */
        $user = $this->getUser();
        $fullName = "{$user->getLastName()} {$user->getFirstName()}";
        /** @var User $deliverer */
        $deliverer = $package->getDeliverer();

        return <<<EOL
            A notification has been sent to {$deliverer->getLastName()} {$deliverer->getFirstName()}.
        EOL;
    }

    /**
     * @return string
     */
    private function onDone(): string
    {
        return <<<EOL
            Notification has been sent.
        EOL;
    }
}
