<?php
declare(strict_types=1);


namespace App\Security;


use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CustomJWTAuthenticator
 *
 * @package App\Security
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CustomJWTAuthenticator extends JWTAuthenticator
{
}
