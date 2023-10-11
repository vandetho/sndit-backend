<?php

namespace App\Command;

use App\Event\User\DeleteUserEvent;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DeleteUserCommand
 *
 * @package App\Command
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[AsCommand('sndit:user:delete')]
class DeleteUserCommand extends Command
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $dispatcher;

    /**
     * DeleteUserCommand constructor.
     *
     * @param UserRepository  $userRepository
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(UserRepository $userRepository, EventDispatcherInterface $dispatcher)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Delete all users that have requested to delete their account')
            ->setHelp(<<<'EOT'
The <info>sndit:user:delete</info> command will delete all users
  <info>php %command.full_name%</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf('Starting deleting all users that should be done on today %s', date('d/m/Y')));
        $users = $this->userRepository->findAllDeleted();
        foreach ($users as $user) {
            $this->dispatcher->dispatch(new DeleteUserEvent($user));
            $output->writeln(sprintf('User "%s" has been deleted.', $user->getFullName()));
        }
        $output->writeln(sprintf('All users that should be done on today %s', date('d/m/Y')));
        return Command::SUCCESS;
    }
}
