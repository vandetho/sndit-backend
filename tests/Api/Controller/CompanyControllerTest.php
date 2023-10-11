<?php
declare(strict_types=1);


namespace App\Tests\Api\Controller;

use App\Constants\Gender;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\EmployeeRepository;
use App\Utils\TokenGenerator;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CompanyControllerTest
 *
 * @package App\Tests\Api\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CompanyControllerTest extends AbstractWebTestCase
{

    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * @return void
     * @throws JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->companyRepository = self::getContainer()->get(CompanyRepository::class);
        $this->employeeRepository = self::getContainer()->get(EmployeeRepository::class);
    }

    /**
     * @throws JsonException
     */
    public function testPost(): void
    {
        $name = $this->faker->company();
        $this->client->request('POST', '/api/companies', [
            'name'      => $name,
        ]);
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertFalse($response->error);
        self::assertEquals($response->message, $this->translator->trans('flash.success.company_created', ['%name%' => $name], 'application'));
        self::assertIsObject($response->data);
        self::assertEquals($response->data->name, $name);
    }

    /**
     * @throws JsonException
     */
    public function testGets(): void
    {
        $this->client->request('GET', "/api/companies", []);
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertFalse($response->error);
        self::assertNull($response->message);
        self::assertIsObject($response->data);
        self::assertIsArray($response->data->companies);
        self::assertIsInt($response->data->totalRows);
    }

    /**
     * @throws JsonException
     */
    public function testGetc(): void
    {
        if (null !== $company = $this->companyRepository->findOneBy([])) {
            $this->client->request('GET', "/api/companies/{$company->getToken()}", []);
            self::assertResponseIsSuccessful();
            $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
            self::assertFalse($response->error);
            self::assertNull($response->message);
            self::assertIsObject($response->data);
            self::assertEquals($response->data->token, $company->getToken());
        }
    }

    /**
     * @throws JsonException
     */
    public function testGetcEmployees(): void
    {
        if (null !== $company = $this->companyRepository->findOneBy([])) {
            $this->client->request('GET', "/api/companies/{$company->getToken()}/employees");
            self::assertResponseIsSuccessful();
            $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
            self::assertFalse($response->error);
            self::assertNull($response->message);
            self::assertIsObject($response->data);
            self::assertIsArray($response->data->employees);
            self::assertIsInt($response->data->totalRows);
        }
    }

    /**
     * @throws JsonException
     */
    public function testEmployees(): void
    {
        if (null !== $company = $this->companyRepository->findOneBy([])) {
            if (null === $user = $this->userRepository->find(2)){
                $user = new User();
                $user->setGender(Gender::UNSPECIFIED);
                $user->setFirstName($this->faker->firstName());
                $user->setLastName($this->faker->lastName());
                $user->setPhoneNumber('85578377799');
                $user->setMarking(['active' => 1]);
                $user->setToken(TokenGenerator::generate(['symbols' => false, 'length' => 32]));
                $this->userRepository->save($user);
            }
            if (null === $this->employeeRepository->findOneBy(['company' => $company, 'user' => $user])) {
                $this->client->jsonRequest('POST', "/api/companies/{$company->getId()}/employees", [
                    'user' => $user->getToken(),
                ]);
                self::assertResponseIsSuccessful();
                self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
                $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
                self::assertFalse($response->error);
                self::assertEquals($response->message, $this->translator->trans('flash.success.employee_added', [], 'application'));
            }
            self::assertTrue(true);
        }
    }
}
