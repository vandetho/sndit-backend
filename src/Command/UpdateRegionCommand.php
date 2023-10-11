<?php
declare(strict_types=1);



namespace App\Command;


use App\Entity\Region;
use App\Repository\RegionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class UpdateRegionCommand
 *
 * @package App\Command
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[AsCommand('sndit:update-regions')]
class UpdateRegionCommand extends Command
{
    public const REGION = [
        ['name' => 'Phnom Penh', 'iso_country_code' => 'KH'],
        ['name' => 'Banteay Meanchey', 'iso_country_code' => 'KH'],
        ['name' => 'Battambang', 'iso_country_code' => 'KH'],
        ['name' => 'Kampong Cham', 'iso_country_code' => 'KH'],
        ['name' => 'Kampong Chhnang,', 'iso_country_code' => 'KH'],
        ['name' => 'Kampong Speu', 'iso_country_code' => 'KH'],
        ['name' => 'Kampong Thom', 'iso_country_code' => 'KH'],
        ['name' => 'Kampot', 'iso_country_code' => 'KH'],
        ['name' => 'Kandal', 'iso_country_code' => 'KH'],
        ['name' => 'Koh Kong', 'iso_country_code' => 'KH'],
        ['name' => 'Kep Province', 'iso_country_code' => 'KH'],
        ['name' => 'Kratié', 'iso_country_code' => 'KH'],
        ['name' => 'Mondulkiri', 'iso_country_code' => 'KH'],
        ['name' => 'Oddar Meanchey', 'iso_country_code' => 'KH'],
        ['name' => 'Pailin', 'iso_country_code' => 'KH'],
        ['name' => 'Preah Sihanouk', 'iso_country_code' => 'KH'],
        ['name' => 'Preah Vihear', 'iso_country_code' => 'KH'],
        ['name' => 'Pursat', 'iso_country_code' => 'KH'],
        ['name' => 'Prey Veng', 'iso_country_code' => 'KH'],
        ['name' => 'Ratanakiri', 'iso_country_code' => 'KH'],
        ['name' => 'Siem Reap', 'iso_country_code' => 'KH'],
        ['name' => 'Stung Treng', 'iso_country_code' => 'KH'],
        ['name' => 'Svay Rieng', 'iso_country_code' => 'KH'],
        ['name' => 'Takéo', 'iso_country_code' => 'KH'],
        ['name' => 'Tboung Khmum', 'iso_country_code' => 'KH'],
    ];

    /**
     * @var RegionRepository
     */
    private RegionRepository $regionRepository;

    /**
     * CreateRegionCommand constructor.
     *
     * @param RegionRepository $regionRepository
     */
    public function __construct(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('This command will generate all regions');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->newLine();
        $io->success('Starting to create regions');
        foreach ($io->progressIterate($this::REGION) as $REGION)
        {
            $region = new Region();
            $region->setName($REGION['name']);
            $region->setIsoCountryCode($REGION['iso_country_code']);
            $this->regionRepository->save($region, false);
        }
        $this->regionRepository->flush();
        $io->success('Region creation completed');

        $io->newLine();

        return Command::SUCCESS;
    }
}
