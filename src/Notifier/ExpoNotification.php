<?php
declare(strict_types=1);


namespace App\Notifier;

use App\Entity\UserNotificationToken;
use App\Model\NotificationMessage;
use App\Repository\UserNotificationTokenRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

/**
 * Class ExpoNotification
 *
 * @package App\Notifier
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class ExpoNotification implements ExpoNotificationInterface
{
    public const INVALID_EXPO_ENDPOINT_MESSAGE = 'Invalid Expo API endpoint configured.';
    public const CONNECT_EXCEPTION_MESSAGE = 'Connection could not be established.';
    public const UNKNOWN_EXCEPTION_MESSAGE = 'A Exception was thrown. Neither a ConnectionException nor a ClientException.';

    /**
     * @var UserNotificationTokenRepository
     */
    private UserNotificationTokenRepository $notificationTokenRepository;

    /**
     * @var Client
     */
    private Client $httpClient;

    /**
     * @var string
     */
    private string $expoApiUrl;

    /**
     * ExpoNotification constructor.
     *
     * @param UserNotificationTokenRepository $notificationTokenRepository
     * @param string                          $expoApiUrl
     */
    public function __construct(UserNotificationTokenRepository $notificationTokenRepository, string $expoApiUrl = 'https://exp.host/--/api/v2/push/send')
    {
        $this->httpClient = new Client();
        $this->expoApiUrl = $expoApiUrl;
        $this->notificationTokenRepository = $notificationTokenRepository;
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function sendNotification(string $to, string $message, string $title = '', array $data = []): NotificationMessage
    {
        $notificationMessage = new NotificationMessage();
        $notificationMessage
            ->setTo($to)
            ->setBody($message);

        if ($title !== '') {
            $notificationMessage->setTitle($title);
        }

        if (count($data) > 0) {
            $notificationMessage->setData($data);
        }

        $httpResponse = $this->sendNotificationHttp($notificationMessage);

        $this->handleHttpResponse([$httpResponse], [$notificationMessage]);

        return $notificationMessage;
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function sendNotifications(array $notifications): array
    {
        $notificationMessages = $this->createNotificationMessages($notifications);

        $httpResponse = $this->sendNotificationsHttp($notificationMessages);

        return $this->handleHttpResponse($httpResponse, $notificationMessages);
    }

    /**
     * Sends an HTTP request to the expo API to issue a push notification.
     *
     * @param NotificationMessage $notificationMessage
     *
     * @return mixed
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    private function sendNotificationHttp(NotificationMessage $notificationMessage): mixed
    {
        $headers = [
            'accept'          => 'application/json',
            'accept-encoding' => 'gzip, deflate',
            'content-type'    => 'application/json',
        ];

        $requestData = [
            'headers' => $headers,
            'body'    => json_encode($notificationMessage->getRequestData(), JSON_THROW_ON_ERROR),
        ];

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->expoApiUrl,
                $requestData
            );

            $responseData = json_decode($response->getBody()->read(1024), true, 512, JSON_THROW_ON_ERROR);

            return $responseData['data'];

        }
        catch (ClientException $e) {
            $exceptionMessage = $e->getResponse()->getStatusCode().': '.$e->getResponse()->getReasonPhrase();

            if ($e->hasResponse()) {
                $content = json_decode($e->getResponse()->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                if (isset($content['data']['details']['error']) && $content['data']['details']['error'] === 'DeviceNotRegistered') {
                    /** @var UserNotificationToken $notification */
                    $notification = $this->notificationTokenRepository->findByToken($notificationMessage->getTo());
                    $notification->setValid(false);
                    $this->notificationTokenRepository->update($notification);
                }

            }
            $exceptionResponse = [
                'status'  => 'error',
                'message' => $exceptionMessage,
                'details' => [self::INVALID_EXPO_ENDPOINT_MESSAGE],
            ];
        } catch (ConnectException) {
            $exceptionMessage = 'No Response.';

            $exceptionResponse = [
                'status'  => 'error',
                'message' => $exceptionMessage,
                'details' => [self::CONNECT_EXCEPTION_MESSAGE],
            ];
        } catch (Exception) {
            $exceptionMessage = 'An unknown Exception occurred.';

            $exceptionResponse = [
                'status'  => 'error',
                'message' => $exceptionMessage,
                'details' => [self::UNKNOWN_EXCEPTION_MESSAGE],
            ];
        }

        return $exceptionResponse;
    }

    /**
     * Sends an HTTP request to the expo API to issue multiple push notifications.
     *
     * @param array $notificationMessages
     *
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function sendNotificationsHttp(array $notificationMessages): array
    {
        $headers = [
            'accept'          => 'application/json',
            'accept-encoding' => 'gzip, deflate',
            'content-type'    => 'application/json',
        ];

        $requestData = [
            'headers' => $headers,
            'body'    => json_encode($this->createRequestBody($notificationMessages), JSON_THROW_ON_ERROR),
        ];

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->expoApiUrl,
                $requestData
            );

            $responseData = json_decode($response->getBody()->read(1024), true, 512, JSON_THROW_ON_ERROR);

            return $responseData['data'];
        } catch (ClientException $e) {
            $exceptionMessage = $e->getResponse()->getStatusCode().': '.$e->getResponse()->getReasonPhrase();

            $exceptionResponse = [
                'status'  => 'error',
                'message' => $exceptionMessage,
                'details' => [self::INVALID_EXPO_ENDPOINT_MESSAGE],
            ];
        } catch (ConnectException) {
            $exceptionMessage = 'No Response.';

            $exceptionResponse = [
                'status'  => 'error',
                'message' => $exceptionMessage,
                'details' => [self::CONNECT_EXCEPTION_MESSAGE],
            ];
        } catch (Exception) {
            $exceptionMessage = 'An unknown Exception occurred.';

            $exceptionResponse = [
                'status'  => 'error',
                'message' => $exceptionMessage,
                'details' => [self::UNKNOWN_EXCEPTION_MESSAGE],
            ];
        }

        $exceptionResponseArray = [];
        $i = 0;
        while ($i < count($notificationMessages)) {
            $exceptionResponseArray[] = $exceptionResponse;
            $i++;
        }

        return $exceptionResponseArray;
    }

    /**
     * Maps the given tokens and messages to proper NotificationMessages.
     *
     * @param array $notifications = [
     *      'to' => (string) notification token,
     *      'body' => (string) notification body,
     *      'title' => (string) notification title,
     *      'data' => (array) notification data,
     * ]
     * @return array
     */
    private function createNotificationMessages(array $notifications): array
    {
        $notificationMessages = [];

        foreach ($notifications as $notification) {
            $message = new NotificationMessage();
            $message
                ->setTo($notification['to'])
                ->setBody($notification['body']);

            if ($notification['title'] !== '') {
                $message->setTitle($notification['title']);
            }

            if (is_array($notification['data']) && count($notification['data']) > 0) {
                $message->setData($notification['data']);
            }

            $notificationMessages[] = $message;
        }

        return $notificationMessages;
    }

    /**
     * Creates a detailed response array for the given notifications.
     *
     * param array $httpResponse
     * param array $notificationMessages
     *
     * @param array $httpResponse
     * @param array $notificationMessages
     * @return array
     */
    private function handleHttpResponse(
        array $httpResponse,
        array $notificationMessages
    ): array {
        foreach ($httpResponse as $key => $httpResponseDetails) {
            $wasSuccessful = false;

            if ($httpResponseDetails['status'] !== 'error') {
                $wasSuccessful = true;
            } else {
                if ($httpResponseDetails['message']
                    && $httpResponseDetails['message'] !== ''
                ) {
                    $notificationMessages[$key]->setResponseMessage($httpResponseDetails['message']);
                }

                if ($httpResponseDetails['details']
                    && count($httpResponseDetails['details']) > 0
                ) {
                    $notificationMessages[$key]->setResponseDetails($httpResponseDetails['details']);
                }
            }

            $notificationMessages[$key]->setWasSuccessful($wasSuccessful);
        }

        return $notificationMessages;
    }

    /**
     * Creates an array of requestData arrays from given NotificationMessages.
     * Returns JSON if wanted.
     *
     * @param array $notificationMessages
     *
     * @return array
     */
    private function createRequestBody(array $notificationMessages): array
    {
        $requestData = [];

        foreach ($notificationMessages as $message) {
            $requestData[] = $message->getRequestData();
        }

        return $requestData;
    }
}
