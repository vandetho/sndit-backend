<?php

namespace App\Command;

use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class CreateUserCommand
 *
 * @package App\Command
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[AsCommand('sndit:user:create')]
class CreateUserCommand extends AbstractUserCommand
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('firstName', InputArgument::REQUIRED, 'First name'),
                new InputArgument('lastName', InputArgument::REQUIRED, 'Last name'),
                new InputArgument('gender', InputArgument::REQUIRED, 'Gender'),
                new InputArgument('dob', InputArgument::REQUIRED, 'Day of birth'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('disable', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ])
            ->setHelp(<<<EOT
The <info>sndit:user:create</info> command creates a user:
  <info>php %command.full_name% matthieu</info>
This interactive shell will ask you for an email and then a password.
You can alternatively specify the email and password as the second and third arguments:
  <info>php %command.full_name% matthieu matthieu@example.com mypassword</info>
You can create a super admin via the super-admin flag:
  <info>php %command.full_name% admin --super-admin</info>
You can create an inactive user (will not be able to log in):
  <info>php %command.full_name% thibault --inactive</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $email = $input->getArgument('email');
        $gender = $input->getArgument('gender');
        $dob = DateTimeImmutable::createFromFormat('d/m/Y', $input->getArgument('dob'));
        $disable = $input->getOption('disable');
        $superAdmin = $input->getOption('super-admin');

        $this->userManipulator->create($password, $firstName, $lastName, $email, $gender, $dob, !$disable, $superAdmin);

        $output->writeln(sprintf('Created user <comment>%s %s</comment>', $lastName, $firstName));

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(static function ($email) {
                if (empty($email)) {
                    throw new RuntimeException('Email can not be empty');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(static function ($password) {
                if (empty($password)) {
                    throw new RuntimeException('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        if (!$input->getArgument('firstName')) {
            $question = new Question('Please enter the first name:');
            $question->setValidator(static function (string $firstName) {
                if (empty($firstName)) {
                    throw new RuntimeException('First name can not be empty');
                }

                return $firstName;
            });
            $questions['firstName'] = $question;
        }

        if (!$input->getArgument('lastName')) {
            $question = new Question('Please enter the last name:');
            $question->setValidator(static function (string $lastName) {
                if (empty($lastName)) {
                    throw new RuntimeException('Last name can not be empty');
                }

                return $lastName;
            });
            $questions['lastName'] = $question;
        }

        if (!$input->getArgument('gender')) {
            $question = new Question('Please enter the gender:');
            $question->setValidator(static function (string $gender) {
                if (empty($gender)) {
                    throw new RuntimeException('Last name can not be empty');
                }

                return $gender;
            });
            $questions['gender'] = $question;
        }

        if (!$input->getArgument('dob')) {
            $question = new Question('Please enter the day of birth:');
            $question->setValidator(static function (string $dob) {
                if (empty($dob)) {
                    throw new RuntimeException('Day of birth can not be empty');
                }

                return $dob;
            });
            $questions['dob'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
