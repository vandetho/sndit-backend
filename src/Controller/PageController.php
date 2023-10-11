<?php
declare(strict_types=1);


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecuredAreaController
 *
 * @package App\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PageController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(
        path: '/{route}',
        name: 'sndit_default_page',
        requirements: ['route' => '^(?!.*(api|login|logout|locales)).*'],
        defaults: ['route' => null],
        methods: ['GET']
    )]
    public function page(): Response
    {
        return $this->render('base.html.twig');
    }
}
