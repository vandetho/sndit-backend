<?php

namespace App\Command;

use App\Utils\UserManipulator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DemoteUserCommand
 *
 * @package App\Command
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[AsCommand('sndit:user:demote')]
class DemoteUserCommand extends RoleCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Demote a user by removing a role')
            ->setHelp(<<<'EOT'
The <info>sndit:user:demote</info> command demotes a user by removing a role

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
            $manipulator->demote($email);
            $output->writeln(sprintf('User "%s" has been demoted as a simple user. This change will not apply until the user logs out and back in again.', $email));
        } elseif ($manipulator->removeRole($email, $role)) {
            $output->writeln(sprintf('Role "%s" has been removed from user "%s". This change will not apply until the user logs out and back in again.', $role, $email));
        } else {
            $output->writeln(sprintf('User "%s" did not have "%s" role.', $email, $role));
        }
    }
}
