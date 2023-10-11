<?php

namespace App\Command;

use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class DeactivateUserCommand
 *
 * @package App\Command
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
#[AsCommand('sndit:user:deactivate')]
class DeactivateUserCommand extends AbstractUserCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Deactivate a user')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The e-mail'),
            ])
            ->setHelp(<<<'EOT'
The <info>sndit:user:deactivate</info> command deactivates a user (will not be able to log in)

  <info>php %command.full_name% matthieu</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        $this->userManipulator->deactivate($email);

        $output->writeln(sprintf('User "%s" has been deactivated.', $email));

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Please choose a e-mail:');
            $question->setValidator(static function ($email) {
                if (empty($email)) {
                    throw new RuntimeException('E-mail can not be empty');
                }

                return $email;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);

            $input->setArgument('email', $answer);
        }
    }
}
