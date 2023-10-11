<?php
declare(strict_types=1);


namespace App\Security;


use App\Entity\InternalLastLogin;
use App\Entity\InternalUser;
use App\Repository\InternalUserRepository;
use JsonException;
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
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CustomInternalAuthenticator
 *
 * @package App\Security
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CustomInternalAuthenticator extends AbstractAuthenticator
{
    /**
     * @var InternalUserRepository
     */
    private InternalUserRepository $userRepository;

    /**
     * @var AuthenticationSuccessHandlerInterface|null
     */
    private ?AuthenticationSuccessHandlerInterface $successHandler;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * CustomInternalAuthenticator constructor.
     *
     * @param InternalUserRepository                $userRepository
     * @param TranslatorInterface                   $translator
     * @param AuthenticationSuccessHandlerInterface $successHandler
     */
    public function __construct(
        InternalUserRepository $userRepository,
        TranslatorInterface $translator,
        AuthenticationSuccessHandlerInterface $successHandler,
    ) {
        $this->userRepository = $userRepository;
        $this->successHandler = $successHandler;
        $this->translator = $translator;
    }

    /**
     * @throws JsonException
     */
    protected function getCredentials(Request $request)
    {
        if ($request->getContent()) {
            return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        }
        throw new BadRequestHttpException('The e-mail is missing');
    }

    /**
     * @param Request $request
     * @return Passport
     * @throws JsonException
     */
    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        return new Passport(
            new UserBadge($credentials['email'], function ($userIdentifier) {
                if (null === $user = $this->userRepository->findByIdentifier($userIdentifier)) {
                    $message = $this->translator->trans('flash.errors.user_email_not_found', ['%email%' => $userIdentifier], 'application');
                    throw new CustomUserMessageAuthenticationException($message);
                }

                if (!$user->isEnabled()) {
                    $message = $this->translator->trans('flash.errors.user_not_enabled', ['%email%' => $userIdentifier], 'application');
                    throw new CustomUserMessageAuthenticationException($message);
                }

                return $user;
            }),
            new PasswordCredentials($credentials['password'])
        );
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'sndit_internal_api_login_check' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var InternalUser $user */
        $user = $token->getUser();
        $this->createLastLogin($user, $request->getClientIp());
        $this->userRepository->save($user);

        return $this->successHandler?->onAuthenticationSuccess($request, $token);

    }

    /**
     * @param InternalUser $user
     * @param string       $clientIp
     * @return void
     */
    private function createLastLogin(InternalUser $user, string $clientIp): void
    {
        $lastLogin = new InternalLastLogin();
        $lastLogin->setIp($clientIp);
        $lastLogin->setInternalUser($user);
        $user->setLastLogin($lastLogin->getCreatedAt());
        $this->userRepository->save($lastLogin);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse([
            'error'   => true,
            'message' => $exception->getMessage(),
            'code'    => Response::HTTP_BAD_REQUEST,
        ], Response::HTTP_BAD_REQUEST);
    }
}
