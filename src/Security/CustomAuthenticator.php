<?php
declare(strict_types=1);


namespace App\Security;


use App\Entity\LastLogin;
use App\Entity\OTP;
use App\Entity\User;
use App\OTP\MoviderOTP;
use App\Repository\UserRepository;
use App\Utils\TokenGenerator;
use Doctrine\ORM\NonUniqueResultException;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CustomAuthenticator
 *
 * @package App\Security
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CustomAuthenticator extends AbstractAuthenticator
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var AuthenticationSuccessHandlerInterface|null
     */
    private ?AuthenticationSuccessHandlerInterface $successHandler;

    /**
     * @var MoviderOTP
     */
    private MoviderOTP $moviderOTP;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var JWTTokenManagerInterface
     */
    private JWTTokenManagerInterface $JWTTokenManager;

    /**
     * @var RefreshTokenGeneratorInterface
     */
    private RefreshTokenGeneratorInterface $refreshTokenGenerator;

    /**
     * @var int
     */
    private int $refreshTokenTtl;

    /**
     * CustomAuthenticator constructor.
     *
     * @param UserRepository                        $userRepository
     * @param TranslatorInterface                   $translator
     * @param MoviderOTP                            $moviderOTP
     * @param JWTTokenManagerInterface              $JWTTokenManager
     * @param RefreshTokenGeneratorInterface        $refreshTokenGenerator
     * @param AuthenticationSuccessHandlerInterface $successHandler
     * @param int                                   $refreshTokenTtl
     */
    public function __construct(
        UserRepository $userRepository,
        TranslatorInterface $translator,
        MoviderOTP $moviderOTP,
        JWTTokenManagerInterface $JWTTokenManager,
        RefreshTokenGeneratorInterface $refreshTokenGenerator,
        AuthenticationSuccessHandlerInterface $successHandler,
        int $refreshTokenTtl
    ) {
        $this->userRepository = $userRepository;
        $this->successHandler = $successHandler;
        $this->moviderOTP = $moviderOTP;
        $this->translator = $translator;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->refreshTokenGenerator = $refreshTokenGenerator;
        $this->refreshTokenTtl = $refreshTokenTtl;
    }

    /**
     * @throws JsonException
     */
    protected function getCredentials(Request $request)
    {
        if ($request->getContent()) {
            return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        }
        throw new BadRequestHttpException('The phone number is missing');
    }

    /**
     * @param Request $request
     * @return Passport
     * @throws JsonException
     * @throws NonUniqueResultException
     */
    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);
        if (null === $user = $this->userRepository->findByIdentifier($credentials['phoneNumber'])) {
            $user = $this->userRepository->create();
            $user->setPhoneNumber($credentials['phoneNumber']);
            $user->setCountryCode($credentials['countryCode']);
            $user->setToken(TokenGenerator::generate(['symbols' => false, 'length' => 32]));
            $this->userRepository->updateUser($user);
            $message = $this->translator->trans('flash.errors.not_registered', [], 'application');
            throw new CustomUserMessageAuthenticationException($message, ['user' => $user]);
        }

        if ($user->isVerified()) {
            return new SelfValidatingPassport(new UserBadge($credentials['phoneNumber']));
        }

        $message = $this->translator->trans('flash.errors.not_registered', [], 'application');
        throw new CustomUserMessageAuthenticationException($message, ['user' => $user]);

    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'sndit_api_login_check' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $firewallName
     * @return Response|null
     * @throws GuzzleException
     * @throws JsonException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();
        $this->createLastLogin($user, $request->getClientIp());
        $this->sendOTP($user);
        $this->userRepository->save($user);

        return $this->successHandler?->onAuthenticationSuccess($request, $token);

    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     * @return Response|null
     * @throws GuzzleException
     * @throws JsonException
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {

        $user = $exception->getMessageData()['user'];
        $this->sendOTP($user);
        $this->createLastLogin($user, $request->getClientIp());

        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($user, $this->refreshTokenTtl);
        $this->userRepository->save($refreshToken);

        return new JsonResponse([
            'error'   => true,
            'message' => $exception->getMessage(),
            'code'    => Response::HTTP_BAD_REQUEST,
            'data'    => [
                'token'        => $this->JWTTokenManager->create($user),
                'refreshToken' => $refreshToken->getRefreshToken(),
            ],
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param User $user
     * @return void
     * @throws GuzzleException
     * @throws JsonException
     */
    private function sendOTP(User $user): void
    {
        $data = $this->moviderOTP->generateOTP($user->getSanitizePhoneNumber());

        $otp = new OTP();
        $otp->setPhoneNumber($user->getSanitizePhoneNumber());
        $otp->setUser($user);
        $otp->setRequestId($data['request_id']);
        $user->setLastOTP($otp);
        $this->userRepository->save($otp, false);
    }

    /**
     * @param User   $user
     * @param string $clientIp
     * @return void
     */
    private function createLastLogin(User $user, string $clientIp): void
    {
        $lastLogin = new LastLogin();
        $lastLogin->setIp($clientIp);
        $lastLogin->setUser($user);
        $user->setLastLogin($lastLogin->getCreatedAt());
        $this->userRepository->save($lastLogin);
    }
}
