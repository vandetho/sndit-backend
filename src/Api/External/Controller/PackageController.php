<?php
declare(strict_types=1);


namespace App\Api\External\Controller;


use App\Api\Controller\AbstractController;
use App\Builder\PackageBuilder;
use App\DTO\Package;
use App\DTO\PackageHistory;
use App\Event\Package\CheckPackageExistEvent;
use App\Model\ErrorResponseData;
use App\Repository\PackageHistoryRepository;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PackageController
 *
 * @package App\Api\External\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[OA\Tag(name: 'External Package')]
#[Route(path: '/packages', name: 'sndit_external_api_packages_')]
class PackageController extends AbstractController
{
    /**
     * @param int|string $idOrToken
     * @return JsonResponse
     */
    #[OA\Parameter(name: 'idOrToken', description: 'Id or token of package', in: 'path', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when package has been given to deliverer',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Package::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(path: '/{idOrToken}', name: 'getc', methods: ['GET'])]
    public function getc(int|string $idOrToken): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return $this->generateSuccessResponse(PackageBuilder::buildDTO($event->getPackage()));
    }

    /**
     * @param int|string               $idOrToken
     * @param Request                  $request
     * @param PackageHistoryRepository $packageHistoryRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(
        name: 'idOrToken',
        description: 'Id or token of package',
        in: 'path',
        schema: new OA\Schema(
            oneOf: [new OA\Schema(type: 'string'), new OA\Schema(type: 'integer')]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when package histories are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: PackageHistory::class)),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when package is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(path: '/{idOrToken}/histories', name: 'histories', methods: ['GET'])]
    public function histories(int|string $idOrToken, Request $request, PackageHistoryRepository $packageHistoryRepository): JsonResponse
    {
        /** @var CheckPackageExistEvent $event */
        $event = $this->dispatchEvent(CheckPackageExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $criteria = $this->getCriteria([], $request);
        $criteria['package'] = $event->getPackage();

        return $this->generateTableData(
            $packageHistoryRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $packageHistoryRepository->countByCriteria($criteria),
            'histories'
        );
    }
}
