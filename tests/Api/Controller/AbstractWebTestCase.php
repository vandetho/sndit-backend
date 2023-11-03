<?php
declare(strict_types=1);


namespace App\Tests\Api\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\TokenGenerator;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Faker\Factory;
use Faker\Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractWebTestCase
 *
 * @package App\Tests\Api\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
abstract class AbstractWebTestCase extends WebTestCase
{
    /**
     * @var Generator
     */
    protected Generator $faker;

    /**
     * @var KernelBrowser
     */
    protected KernelBrowser $client;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * @return void
     * @throws JsonException
     */
    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->createAuthenticatedClient();
        $this->translator = self::getContainer()->get(TranslatorInterface::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    /**
     * @return void
     * @throws JsonException
     */
    protected function createAuthenticatedClient(): void
    {
        $this->client = static::createClient();
        $phoneNumber = '87717377799';
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['phoneNumber' => $phoneNumber, 'countryCode' => '+855'], JSON_THROW_ON_ERROR),
        );
        $data = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $token = $this->client->getResponse()->getStatusCode() === Response::HTTP_OK ? $data->token : $data->data->token;
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
        if ($this->client->getResponse()->getStatusCode() !== Response::HTTP_OK) {
            $this->client->request('PUT', '/api/users/current', [
                'firstName' => 'Vandeth',
                'lastName'  => 'Tho',
                'dob'       => '1989-09-09'
            ]);
        }
    }
}
