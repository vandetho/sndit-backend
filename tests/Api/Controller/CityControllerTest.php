<?php
declare(strict_types=1);


namespace App\Tests\Api\Controller;

use App\Api\Controller\CityController;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * Class CityControllerTest
 *
 * @package App\Tests\Api\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CityControllerTest extends AbstractWebTestCase
{

    /**
     * @throws JsonException
     */
    public function testGets(): void
    {
        $this->client->request('GET', '/api/cities');
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertFalse($response->error);
        self::assertNull($response->message);
        self::assertIsArray($response->data->cities);
        self::assertIsInt($response->data->totalRows);
    }
}
