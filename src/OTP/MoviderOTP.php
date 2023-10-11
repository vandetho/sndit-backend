<?php
declare(strict_types=1);



namespace App\OTP;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

/**
 * Class MoviderOTP
 *
 * @package App\Security
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class MoviderOTP
{
    public const MOVIDER_VERIFY_URL = 'https://api.movider.co/v1/verify';
    public const MOVIDER_ACKNOWLEDGE_VERIFY_URL = 'https://api.movider.co/v1/verify/acknowledge';
    public const MOVIDER_CANCEL_VERIFY_URL = 'https://api.movider.co/v1/verify/cancel';
    public const OTP_CODE_LENGTH = 6;
    public const OTP_FROM = 'Keilla';
    public const OTP_LANGUAGE = 'en-us';

    /**
     * @var string
     */
    private string $moviderApiKey;

    /**
     * @var string
     */
    private string $moviderApiSecret;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * MoviderOTP constructor.
     *
     * @param string $moviderApiKey
     * @param string $moviderApiSecret
     */
    public function __construct(
        string $moviderApiKey,
        string $moviderApiSecret
    ) {
        $this->moviderApiKey = $moviderApiKey;
        $this->moviderApiSecret = $moviderApiSecret;
        $this->client = new Client();
    }

    /**
     * @param string $phoneNumber
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function generateOTP(string $phoneNumber): array
    {
        $result = $this->client->post(self::MOVIDER_VERIFY_URL, [
            'form_params' => [
                'to'          => $phoneNumber,
                'api_key'     => $this->moviderApiKey,
                'api_secret'  => $this->moviderApiSecret,
                'code_length' => self::OTP_CODE_LENGTH,
                'from'        => self::OTP_FROM,
                'language'    => self::OTP_LANGUAGE,
            ],
        ]);

        return json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Verify the otp if it is valid or not
     *
     * @param string $otp
     * @param string $requestId
     *
     * @return array
     *
     * @throws JsonException
     * @throws GuzzleException
     */
    public function isValidOTP(string $otp, string $requestId): array
    {
        $result = $this->client->post(self::MOVIDER_ACKNOWLEDGE_VERIFY_URL, [
            'form_params' => [
                'api_key'    => $this->moviderApiKey,
                'api_secret' => $this->moviderApiSecret,
                'request_id' => $requestId,
                'code'       => $otp,
            ],
        ]);

        return json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Cancel the otp verification
     *
     * @param string $requestId
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function cancel(string $requestId): array
    {
        $result = $this->client->post(self::MOVIDER_CANCEL_VERIFY_URL, [
            'form_params' => [
                'api_key'    => $this->moviderApiKey,
                'api_secret' => $this->moviderApiSecret,
                'request_id' => $requestId,
            ],
        ]);

        return json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}
