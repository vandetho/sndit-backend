<?php
declare(strict_types=1);


namespace App\Controller;


use App\Entity\User;
use App\Utils\ResponseGeneratorInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SecurityController
 *
 * @package App\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class SecurityController extends AbstractController
{
    /**
     * @param Request                    $request
     * @param TranslatorInterface        $translator
     * @param ResponseGeneratorInterface $responseGenerator
     * @return Response
     */
    #[Route(path: '/login', name: 'sndit_security_login', methods: ['GET', 'POST'])]
    public function login(Request $request, TranslatorInterface $translator, ResponseGeneratorInterface $responseGenerator): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('base.html.twig');
        }

        $user = $this->getUser();
        if ($user instanceof User) {
            return $responseGenerator->generateSuccess([
                'phoneNumber' => $user->getPhoneNumber(),
                'roles'    => $user->getRoles(),
            ]);
        }
        $message = $translator->trans('Invalid credentials.', [], 'security');

        return $responseGenerator->generateError($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    #[Route(path: '/logout', name: 'sndit_security_logout', methods: ['GET', 'POST'])]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
