<?php
declare(strict_types=1);


namespace App\Tests\Api\Controller;

use App\Repository\CompanyRepository;
use App\Repository\PackageHistoryRepository;
use App\Repository\PackageRepository;
use App\Workflow\Status\PackageStatus;
use Doctrine\ORM\NonUniqueResultException;
use JsonException;

/**
 * Class PackageControllerTest
 *
 * @package App\Tests\Api\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageControllerTest extends AbstractWebTestCase
{
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var PackageRepository
     */
    private PackageRepository $packageRepository;

    /**
     * @var PackageHistoryRepository
     */
    private PackageHistoryRepository $packageHistoryRepository;

    /**
     * @return void
     * @throws JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->companyRepository = self::getContainer()->get(CompanyRepository::class);
        $this->packageRepository = self::getContainer()->get(PackageRepository::class);
        $this->packageHistoryRepository = self::getContainer()->get(PackageHistoryRepository::class);
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function testPost(): void
    {
        if (null !== $company = $this->companyRepository->findOneBy(['user' => 1])) {
            $name = $this->faker->name();
            $this->client->jsonRequest('POST', '/api/packages', ['name' => $name, 'company' => $company->getId(), 'city' => 1]);
            self::assertResponseIsSuccessful();
            $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
            self::assertEquals($response->message, $this->translator->trans('flash.success.package_created', [], 'application'));
            self::assertIsObject($response->data);
            self::assertEquals($response->data->name, $name);
        }
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function testGetc(): void
    {
        if (null !== $package = $this->packageRepository->findOneBy(['id' => 3])) {
            $this->client->request('GET', "/api/packages/{$package->getToken()}", []);
            self::assertResponseIsSuccessful();
            $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
            self::assertFalse($response->error);
            self::assertNull($response->message);
            self::assertIsObject($response->data);
            self::assertEquals($response->data->token, $package->getToken());
        }
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function testHistories(): void
    {
        if (null !== $package = $this->packageRepository->findOneBy([], ['id' => 'desc'])) {
            $this->client->request('GET', "/api/packages/{$package->getToken()}/histories", []);
            self::assertResponseIsSuccessful();
            $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
            self::assertFalse($response->error);
            self::assertNull($response->message);
            self::assertIsObject($response->data);
            self::assertIsArray($response->data->histories);
            self::assertIsInt($response->data->totalRows);
        }
    }

    /**
     * @throws JsonException
     */
    public function testGets(): void
    {
        $this->client->request('GET', "/api/packages", []);
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertFalse($response->error);
        self::assertNull($response->message);
        self::assertIsObject($response->data);
        self::assertIsArray($response->data->packages);
        self::assertIsInt($response->data->totalRows);
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function testDeliveries(): void
    {
        $this->client->request('GET', "/api/packages/deliveries", []);
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertFalse($response->error);
        self::assertNull($response->message);
        self::assertIsObject($response->data);
        self::assertIsArray($response->data->packages);
        self::assertIsInt($response->data->totalRows);

    }

    /**
     * @return void
     * @throws JsonException
     * @throws NonUniqueResultException
     */
    public function testGiveToDeliverer(): void
    {
        if (null !== $package = $this->packageRepository->findOneByState(PackageStatus::WAITING_FOR_DELIVERY)) {
            $this->client->jsonRequest('POST', "/api/packages/{$package->getToken()}/give-to-deliverer", ['employee' => 1]);
            self::assertResponseIsSuccessful();
            $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
            self::assertFalse($response->error);
            self::assertEquals($response->message, $this->translator->trans('flash.success.package_given_to_deliverer', [], 'application'));
        }
    }

    /**
     * @return void
     * @throws JsonException
     * @throws NonUniqueResultException
     */
    public function testDelivered(): void
    {
        if (null !== $package = $this->packageRepository->findOneByState(PackageStatus::ON_DELIVERY)) {
            $this->client->jsonRequest('POST', "/api/packages/{$package->getToken()}/delivered", [
                'latitude' => $this->faker->latitude(),
                'longitude' => $this->faker->longitude(),
            ]);
            self::assertResponseIsSuccessful();
            $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
            self::assertFalse($response->error);
            self::assertEquals($response->message, $this->translator->trans('flash.success.package_delivered', [], 'application'));
        }
    }
}
