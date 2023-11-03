<?php
declare(strict_types=1);



namespace App\Command;


use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Command\GenerateKeyPairCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallationCommand
 *
 * @package App\Command
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[AsCommand('sndit:install')]
class InstallationCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('This command will install all the component');
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrationCommand = $this->getApplication()?->find('doctrine:'.MigrateCommand::getDefaultName());
        $citiesCommand = $this->getApplication()?->find(UpdateCitiesCommand::getDefaultName());
        $jwtLexikCommand = $this->getApplication()?->find(GenerateKeyPairCommand::getDefaultName());

        $migrationCommand->run($input, $output);
        $citiesCommand->run($input, $output);
        $jwtLexikCommand->run($input, $output);

        return Command::SUCCESS;
    }

}
