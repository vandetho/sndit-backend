<?php
declare(strict_types=1);


namespace App\Api\Controller;


use App\Builder\TemplateBuilder;
use App\DTO\Template;
use App\Entity\Employee;
use App\Entity\User;
use App\Event\Template\CheckTemplateExistEvent;
use App\Form\Types\TemplateType;
use App\Model\ErrorResponseData;
use App\Repository\EmployeeRepository;
use App\Repository\TemplateRepository;
use App\Utils\RequestManipulator;
use App\Utils\ResponseGeneratorInterface;
use Doctrine\ORM\NonUniqueResultException;
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
 * Class TemplateController
 *
 * @template App\Api\Controller
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
#[OA\Tag(name: 'Template')]
#[Route(path: '/templates', name: 'sndit_api_templates_')]
class TemplateController extends AbstractController
{
    private TemplateRepository $templateRepository;

    /**
     * TemplateController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param TemplateRepository         $templateRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        TemplateRepository $templateRepository,
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[OA\Parameter(name: "company", description: "Company id", in: "query", schema: new OA\Schema(type: "string"))]
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
        description: 'Return when templates that are on delivery are retrieved',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Template::class)),
        ])
    )]
    #[Route(name: 'gets', methods: ['GET'])]
    public function gets(Request $request): JsonResponse
    {
        $criteria = $this->getCriteria(['company' => null], $request);
        $criteria['user'] = $this->getUser();

        return $this->generateTableData(
            $this->templateRepository->findByCriteria($criteria, $this->getOrderBy($request), ...$this->getOffsetAndLimit($request)),
            $this->templateRepository->countByCriteria($criteria),
            'templates'
        );
    }

    /**
     * @param Request            $request
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: [
            new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'phoneNumber', type: 'string'),
                        new OA\Property(property: 'address', type: 'string'),
                        new OA\Property(property: 'company', type: 'integer'),
                        new OA\Property(property: 'city', type: 'integer'),
                    ]
                )
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Return when the template has been created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Template::class)),
        ])

    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when parameters are missing',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(name: 'post', methods: ['POST'])]
    public function post(Request $request, EmployeeRepository $employeeRepository): JsonResponse
    {
        $template = $this->templateRepository->create();
        $form = $this->createForm(TemplateType::class, $template);
        $form->submit(RequestManipulator::getData($request, $form));
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $template->getCompany()) {
                $message = $this->translator->trans('flash.errors.company_required', [], 'application');

                return $this->generateErrorResponse($message);
            }
            /** @var User $user */
            $user = $this->getUser();
            $template->setCreator($user);
            $this->templateRepository->save($template);
            $message = $this->translator->trans('flash.success.template_created', [], 'application');
            /** @var Employee $employee */
            $employee = $employeeRepository->findByCompanyAndUser($template->getCompany(), $user);

            return $this->generateSuccessResponse(TemplateBuilder::buildDTO($template, $employee), $message, Response::HTTP_CREATED);
        }

        return $this->getMissingParamsResponse();
    }

    /**
     * @param int                $id
     * @param Request            $request
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse
     */
    #[OA\Parameter(name: 'id', description: 'Id of template', in: 'path', schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        content: [
            new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'phoneNumber', type: 'string'),
                        new OA\Property(property: 'address', type: 'string'),
                        new OA\Property(property: 'company', type: 'integer'),
                        new OA\Property(property: 'city', type: 'integer'),
                    ]
                )
            ),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return when the template has been created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'bool', enum: [false]),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'data', ref: new Model(type: Template::class)),
        ])

    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Return when template is not found',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Return when parameters are missing',
        content: new Model(type: ErrorResponseData::class)
    )]
    #[Route(path: '/{id}', name: 'put', methods: ['PUT'])]
    public function put(int $id, Request $request, EmployeeRepository $employeeRepository): JsonResponse
    {
        /** @var CheckTemplateExistEvent $event */
        $event = $this->dispatchEvent(CheckTemplateExistEvent::class, $id);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $template = $event->getTemplate();
        $form = $this->createForm(TemplateType::class, $template);
        $form->submit(RequestManipulator::getData($request, $form));
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $template->getCompany()) {
                $message = $this->translator->trans('flash.errors.company_required', [], 'application');

                return $this->generateErrorResponse($message);
            }
            $this->templateRepository->update($template);
            $message = $this->translator->trans('flash.success.template_updated', ['%id%' => $template->getId()], 'application');

            /** @var Employee $employee */
            $employee = $employeeRepository->findByCompanyAndUser($template->getCompany(), $this->getUser());

            return $this->generateSuccessResponse(TemplateBuilder::buildDTO($template, $employee), $message, Response::HTTP_CREATED);
        }

        return $this->getMissingParamsResponse();
    }
}
