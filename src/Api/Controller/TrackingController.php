<?php

namespace App\Api\Controller;

use App\Builder\TrackingBuilder;
use App\DTO\Tracking;
use App\Form\Types\TrackingFormType;
use App\Model\ErrorResponseData;
use App\Repository\TrackingRepository;
use App\Utils\ResponseGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TrackingController
 *
 * @package App\Api\Controller
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
#[OA\Tag(name: 'Tracking')]
#[Route(path: '/tracking', name: 'sndit_api_tracking_')]
class TrackingController extends AbstractController
{
    /**
     * @var TrackingRepository
     */
    private TrackingRepository $trackingRepository;

    /**
     * TrackingController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param TrackingRepository         $trackingRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        TrackingRepository $trackingRepository
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->trackingRepository = $trackingRepository;
    }



    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: [
            new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'latitude', type: 'numeric'),
                    new OA\Property(property: 'longitude', type: 'numeric'),
                ]
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when the tracking has been saved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Tracking::class)),
        ])

    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when parameters are missing',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(name: 'post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        $tracking = $this->trackingRepository->create();
        $form = $this->createForm(TrackingFormType::class, $tracking);
        $form->submit($request->toArray());
        if ($form->isSubmitted() && $form->isValid()) {
            $tracking->setUser($this->getUser());
            $this->trackingRepository->save($tracking);
            return $this->generateSuccessResponse(data: TrackingBuilder::buildDTO($tracking), statusCode: Response::HTTP_CREATED);
        }

        return $this->getMissingParamsResponse();
    }
}
