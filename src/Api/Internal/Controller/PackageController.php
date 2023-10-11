<?php

namespace App\Api\Internal\Controller;

use App\Api\Controller\AbstractController;
use App\DTO\MonthlyReport;
use App\Model\ErrorResponseData;
use App\Repository\PackageRepository;
use App\Utils\ResponseGeneratorInterface;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PackageController
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
#[OA\Tag(name: 'Internal Packages')]
#[Route(path: '/packages', name: 'sndit_internal_api_packages_')]
class PackageController extends AbstractController
{
    /**
     * @var PackageRepository
     */
    private PackageRepository $packageRepository;

    /**
     * PackageController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param PackageRepository          $packageRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        PackageRepository $packageRepository,
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->packageRepository = $packageRepository;
    }

    /**
     * @return JsonResponse
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when monthlies packages are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: MonthlyReport::class))),
        ])
    )]
    #[Route(path: '/monthlies', name: 'monthlies', methods: ['GET'])]
    public function monthlies(): JsonResponse
    {
        return $this->generateSuccessResponse($this->packageRepository->countMonthly());
    }

    /**
     * @return JsonResponse
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when average monthlies packages are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: MonthlyReport::class))),
        ])
    )]
    #[Route(path: '/average-monthlies', name: 'average_monthlies', methods: ['GET'])]
    public function averageMonthlies(): JsonResponse
    {
        return $this->generateSuccessResponse($this->packageRepository->countAverageMonthly());
    }
}
