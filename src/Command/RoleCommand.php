<?php

namespace App\Command;

use App\Utils\UserManipulator;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class RoleCommand
 *
 * @package App\Command
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
abstract class RoleCommand extends AbstractUserCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The e-mail'),
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');
        $super = (true === $input->getOption('super'));

        if (null !== $role && $super) {
            throw new InvalidArgumentException('You can pass either the role or the --super option (but not both simultaneously).');
        }

        if (null === $role && !$super) {
            throw new RuntimeException('Not enough arguments.');
        }

        $manipulator = $this->userManipulator;
        $this->executeRoleCommand($manipulator, $output, $email, $super, $role);

        return 0;
    }

    /**
     * @param UserManipulator $manipulator
     * @param OutputInterface $output
     * @param string          $email
     * @param bool            $super
     * @param string          $role
     * @see Command
     */
    abstract protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, string $email, bool $super, string $role);

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose a e-mail:');
            $question->setValidator(static function ($email) {
                if (empty($email)) {
                    throw new RuntimeException('Username can not be empty');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $question = new Question('Please choose a role:');
            $question->setValidator(static function ($role) {
                if (empty($role)) {
                    throw new RuntimeException('Role can not be empty');
                }

                return $role;
            });
            $questions['role'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
