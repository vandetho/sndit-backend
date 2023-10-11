<?php
declare(strict_types=1);


namespace App\Api\Controller;

use App\DTO\City;
use App\Repository\CityRepository;
use App\Utils\ResponseGeneratorInterface;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CityController
 *
 * @package App\Api\Controller
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[OA\Tag(name: 'City')]
#[Route(path: 'cities', name: 'sndit_api_cities_')]
class CityController extends AbstractController
{
    /**
     * @var CityRepository
     */
    protected CityRepository $cityRepository;

    /**
     * CityController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param CityRepository             $cityRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        CityRepository $cityRepository
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->cityRepository = $cityRepository;
    }

    /**
     * Get the list of cities
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws NonUniqueResultException
     *
     */
    #[OA\Parameter(
        name: 'offset',
        description: 'specify which data to start from retrieving data',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'The number of result per request',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(name: "sort", description: "Order column", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "order", description: "Order direction", in: "query", schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when the list of cities is found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'bool', enum: [false]),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'data', properties: [
                    new OA\Property(property: 'cities', type: 'array', items: new OA\Items(ref: new Model(type: City::class))),
                    new OA\Property(property: 'totalRows', type: 'integer'),
                ], type: 'object'),
            ]
        )
    )]
    #[Route(name: 'gets', methods: ['GET'])]
    public function gets(Request $request): JsonResponse
    {
        $criteria = $this->getCriteria([], $request);

        return $this->generateTableData(
            $this->cityRepository->findByCriteria(
                $criteria,
                $this->getOrderBy($request),
                ...$this->getOffsetAndLimit($request)
            ),
            $this->cityRepository->countByCriteria($criteria),
            'cities'
        );
    }
}
