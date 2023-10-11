<?php
declare(strict_types=1);



namespace App\Command;


use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class UpdateCitiesCommand
 *
 * @package App\Command
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[AsCommand('sndit:update-cities')]
class UpdateCitiesCommand extends Command
{
    public const CITIES = [
        ['region_id' => 1, 'name' => 'Phnom Penh'],
        ['region_id' => 2, 'name' => 'Sisophon'],
        ['region_id' => 2, 'name' => 'Poi Pet'],
        ['region_id' => 3, 'name' => 'Battambang'],
        ['region_id' => 4, 'name' => 'Kompong Cham'],
        ['region_id' => 5, 'name' => 'Kompong Chhnang'],
        ['region_id' => 6, 'name' => 'Chbar Mon'],
        ['region_id' => 7, 'name' => 'Stueng Saen'],
        ['region_id' => 8, 'name' => 'Kampot'],
        ['region_id' => 9, 'name' => 'Takmao'],
        ['region_id' => 10, 'name' => 'Khemarak Phoumin'],
        ['region_id' => 11, 'name' => 'Kep'],
        ['region_id' => 12, 'name' => 'Kracheh'],
        ['region_id' => 13, 'name' => 'Saen Monorum'],
        ['region_id' => 14, 'name' => 'Samraong'],
        ['region_id' => 15, 'name' => 'Pailin'],
        ['region_id' => 16, 'name' => 'Preah Sihanouk'],
        ['region_id' => 17, 'name' => 'Preah Vihear'],
        ['region_id' => 18, 'name' => 'Pursat'],
        ['region_id' => 19, 'name' => 'Prey Veng'],
        ['region_id' => 20, 'name' => 'Ban Lung'],
        ['region_id' => 21, 'name' => 'Siem Reap'],
        ['region_id' => 22, 'name' => 'Stung Treng'],
        ['region_id' => 23, 'name' => 'Svay Rieng'],
        ['region_id' => 24, 'name' => 'Doun Keo'],
        ['region_id' => 25, 'name' => 'Soung'],
        ['region_id' => 10, 'name' => 'Koh Kdach'],
        ['region_id' => 10, 'name' => 'Koh Kong'],
        ['region_id' => 7, 'name' => 'Kampong Thom'],
        ['region_id' => 6, 'name' => 'Kampong Speu'],
        ['region_id' => 13, 'name' => 'Mondulkiri'],
        ['region_id' => 14, 'name' => 'Oddar Meanchey'],
        ['region_id' => 14, 'name' => 'Anlong Veng'],
        ['region_id' => 20, 'name' => 'Ratanakiri'],
        ['region_id' => 24, 'name' => 'Takéo'],
    ];

    /**
     * @var CityRepository
     */
    private CityRepository $cityRepository;

    /**
     * @var RegionRepository
     */
    private RegionRepository $regionRepository;

    /**
     * CreateCitiesCommand constructor.
     *
     * @param CityRepository   $cityRepository
     * @param RegionRepository $regionRepository
     */
    public function __construct(CityRepository $cityRepository, RegionRepository $regionRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->regionRepository = $regionRepository;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('This command will generate all cities');
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Running creation regions command');
        $command = $this->getApplication()?->find(UpdateRegionCommand::getDefaultName());
        $command->run($input, $output);

        $io->newLine();
        $io->success('Get all regions order by its id');
        $regions = $this->regionRepository->findBy([], ['id' => 'ASC']);
        $io->newLine();
        $io->success('Starting to create cities');
        foreach ($io->progressIterate($this::CITIES) as $CITY)
        {
            $city = new City();
            $city->setName($CITY['name']);
            $city->setRegion($regions[$CITY['region_id'] - 1]);
            $this->cityRepository->save($city, false);

        }
        $this->cityRepository->flush();
        $io->success('City creation completed');

        $io->newLine();

        return Command::SUCCESS;
    }
}
