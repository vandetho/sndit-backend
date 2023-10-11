<?php

namespace App\Command;

use App\Utils\UserManipulator;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PromoteUserCommand
 *
 * @package App\Command
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[AsCommand('sndit:user:promote')]
class PromoteUserCommand extends RoleCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Promotes a user by adding a role')
            ->setHelp(<<<'EOT'
The <info>sndit:user:promote</info> command promotes a user by adding a role

  <info>php %command.full_name% matthieu ROLE_CUSTOM</info>
  <info>php %command.full_name% --super matthieu</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, string $email, bool $super, string $role): void
    {
        if ($super) {
            $manipulator->promote($email);
            $output->writeln(sprintf('User "%s" has been promoted as a super administrator. This change will not apply until the user logs out and back in again.', $email));
        } elseif ($manipulator->addRole($email, $role)) {
            $output->writeln(sprintf('Role "%s" has been added to user "%s". This change will not apply until the user logs out and back in again.', $role, $email));
        } else {
            $output->writeln(sprintf('User "%s" did already have "%s" role.', $email, $role));
        }
    }
}
