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
class UserControllerTest extends AbstractWebTestCase
{
    /**
     * @throws JsonException
     */
    public function testDelete(): void
    {
        $this->client->request('DELETE', '/api/users/current');
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertFalse($response->error);
        self::assertEquals($response->message, $this->translator->trans('flash.success.user_deleted', [], 'application'));
    }

    /**
     * @throws JsonException
     */
    public function testUndelete(): void
    {
        $this->client->request('POST', '/api/users/current/undelete');
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertFalse($response->error);
        self::assertEquals($response->message, $this->translator->trans('flash.success.user_undeleted', [], 'application'));
    }
}
