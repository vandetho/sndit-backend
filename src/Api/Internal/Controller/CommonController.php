<?php

namespace App\Api\Internal\Controller;

use App\Api\Controller\AbstractController;
use App\Model\ErrorResponseData;
use App\Repository\CompanyRepository;
use App\Repository\PackageRepository;
use App\Repository\UserRepository;
use OpenApi\Attributes as OA;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommonController
 *
 * @package App\Api\Internal\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[Security(name: "bearer")]
#[OA\Parameter(
    name: 'Authorization',
    description: 'JWT Token authentication',
    in: 'header',
    required: true,
    schema: new OA\Schema(type: 'string'),
)]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Return when user is not fully authenticated',
    content: new Model(type: ErrorResponseData::class)
)]
#[OA\Tag(name: 'Common')]
#[Route(path: '/common', name: 'sndit_internal_api_common_')]
class CommonController extends AbstractController
{
    /**
     * @param CompanyRepository $companyRepository
     * @param UserRepository    $userRepository
     * @param PackageRepository $packageRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when summaries is retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', properties: [
                new OA\Property(
                    property: 'tickets',
                    type: 'array',
                    items: new OA\Items(properties: [
                        new OA\Property(property: 'companies', type: 'integer'),
                        new OA\Property(property: 'users', type: 'integer'),
                        new OA\Property(property: 'packages', type: 'integer'),
                        new OA\Property(property: 'monthlyPackages', type: 'integer'),
                    ])
                ),
                new OA\Property(property: 'totalRows', type: 'integer'),
            ], type: 'object'),
        ])
    )]
    #[Route('/summaries', name: 'summaries', methods: ['GET'])]
    public function summaries(CompanyRepository $companyRepository, UserRepository $userRepository, PackageRepository $packageRepository): JsonResponse
    {
        $companies = $companyRepository->count([]);
        $users = $userRepository->count([]);
        $packages = $packageRepository->count([]);
        $monthlyPackages = $packageRepository->countCurrentMonth();

        return $this->generateSuccessResponse([
            'companies' => $companies,
            'users' => $users,
            'packages' => $packages,
            'monthlyPackages' => $monthlyPackages,
        ]);
    }
}
