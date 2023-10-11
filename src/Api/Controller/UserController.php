<?php
declare(strict_types=1);


namespace App\Api\Controller;


use App\Builder\UserBuilder;
use App\DTO\User as UserDTO;
use App\Entity\OTP;
use App\Entity\User;
use App\Entity\UserNotificationToken;
use App\Event\User\CheckUserExistEvent;
use App\Form\Types\AvatarFormType;
use App\Form\Types\UserInformationFormType;
use App\Model\ErrorResponseData;
use App\Model\ResponseData;
use App\OTP\MoviderOTP;
use App\Repository\CompanyRepository;
use App\Repository\UserNotificationTokenRepository;
use App\Repository\UserRepository;
use App\Utils\RequestManipulator;
use App\Utils\ResponseGeneratorInterface;
use DateInterval;
use DateTimeImmutable;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class UserController
 *
 * @package App\APIController
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[Security(name: 'bearer')]
#[OA\Parameter(
    name: "Authorization",
    description: "JWT Token authentication",
    in: "header",
    required: true,
    schema: new OA\Schema(type: "string")
)]
#[OA\Tag(name: "User")]
#[Route(path: '/users', name: 'sndit_api_users_')]
class UserController extends AbstractController
{
    /**
     * @var MoviderOTP
     */
    private MoviderOTP $moviderOTP;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * UserController constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param EventDispatcherInterface   $dispatcher
     * @param UserRepository             $userRepository
     * @param MoviderOTP                 $moviderOTP
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        UserRepository $userRepository,
        MoviderOTP $moviderOTP,
    ) {
        parent::__construct($responseGenerator, $translator, $dispatcher);
        $this->userRepository = $userRepository;
        $this->moviderOTP = $moviderOTP;
    }

    /**
     * Retrieve current user information
     *
     * @param UploaderHelper $uploaderHelper
     * @return JsonResponse
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Response when current user information is successfully retrieved",
        content: new OA\JsonContent(properties: [
            new OA\Property(
                property: "error",
                type: "bool",
                enum: [false]
            ),
            new OA\Property(
                property: "message",
                type: "string"
            ),
            new OA\Property(
                property: "data",
                ref: new Model(type: UserDTO::class)
            ),
        ])
    )]
    #[Route(path: "/current", name: "current", methods: ["GET"])]
    public function current(UploaderHelper $uploaderHelper): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->generateSuccessResponse(UserBuilder::buildDTO($user, $uploaderHelper));
    }

    /**
     * Delete current user information
     *
     * @return JsonResponse
     * @throws Exception
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Response when current user information is successfully deleted",
        content: new Model(type: ResponseData::class)
    )]
    #[Route(path: "/current", name: "delete", methods: ["DELETE"])]
    public function delete(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->isDeleting()) {
            $date = new DateTimeImmutable();
            $user->setDeletedAt($date->add(new DateInterval('P14D')));
            $this->userRepository->update($user);

            return $this->generateSuccessResponse(null, $this->translator->trans('flash.success.user_deleted', [], 'application'));
        }

        return $this->generateErrorResponse($this->translator->trans('flash.errors.user_already_deleted', [], 'application'));
    }

    /**
     * Delete current user information
     *
     * @return JsonResponse
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Response when current user information is successfully undeleted",
        content: new Model(type: ResponseData::class)
    )]
    #[Route(path: "/current/undelete", name: "undelete", methods: ["POST"])]
    public function undelete(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setDeletedAt(null);
        $this->userRepository->update($user);

        return $this->generateSuccessResponse(message: $this->translator->trans('flash.success.user_undeleted', [], 'application'));
    }

    /**
     * Update current user information
     *
     * @param Request        $request
     * @param UploaderHelper $uploaderHelper
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: new OA\JsonContent(properties: [
            new OA\Property(
                property: "firstName",
                description: "User first name",
                type: "string",
            ),
            new OA\Property(
                property: "lastName",
                description: "User last name",
                type: "integer",
            ),
            new OA\Property(
                property: "dob",
                description: "User day of birth",
                type: "date"
            ),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: "Error response when parameter is missing",
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Response when current user information is successfully updated",
        content: new OA\JsonContent(properties: [
            new OA\Property(
                property: "error",
                type: "boolean",
                enum: [false]
            ),
            new OA\Property(
                property: "message",
                type: "string"
            ),
        ])
    )]
    #[Route(path: "/current", name: "put", methods: ["PUT"])]
    public function put(Request $request, UploaderHelper $uploaderHelper): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserInformationFormType::class, $user);
        $form->submit(RequestManipulator::getData($request, $form), false);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->isVerified()) {
                $user->setVerified(true);
                $this->userRepository->update($user);

                return $this->responseGenerator->generateSuccess(UserBuilder::buildDTO($user, $uploaderHelper), $this->translator->trans(
                    'flash.success.user_registered',
                    [],
                    'application'
                ));
            }
            $this->userRepository->update($user);

            return $this->responseGenerator->generateSuccess(UserBuilder::buildDTO($user, $uploaderHelper), $this->translator->trans(
                'flash.success.user_information_updated',
                [],
                'application'
            ));
        }

        return $this->getMissingParamsResponse();
    }

    /**
     * Verify current user's otp
     *
     * @param Request        $request
     * @param UploaderHelper $uploaderHelper
     * @return Response
     *
     * @throws JsonException
     */
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "otp",
                    description: "One time password",
                    type: "string",
                )]
        )
    )
    ]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: "Error response when parameter is missing",
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Response when current user otp is successfully verified",
        content: new OA\JsonContent(properties: [
            new OA\Property(
                property: "error",
                type: "boolean",
                enum: [false]
            ),
            new OA\Property(
                property: "message",
                type: "string"
            ),
        ])
    )]
    #[Route(path: "/verify", name: "verify", methods: ["POST"])]
    public function verify(Request $request, UploaderHelper $uploaderHelper): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();
        try {
            $data = $this->moviderOTP->isValidOTP($request->toArray()['otp'], $user->getLastOTP()?->getRequestId());
        } catch (GuzzleException $exception) {
            $message = $exception->getMessage();
            if ($exception instanceof BadResponseException) {
                $response = $exception->getResponse();
                $data = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
                $message = $data->error->description;
            }

            return $this->generateErrorResponse($message);

        }
        /** @var OTP $otp */
        $otp = $user->getLastOTP();
        $otp->setPrice($data['price']);
        $this->userRepository->update($otp, false);
        $this->userRepository->update($user);
        $this->userRepository->flush();

        return $this->generateSuccessResponse(UserBuilder::buildDTO($user, $uploaderHelper), $this->translator->trans('flash.success.phone_number_verified', [], 'application'));
    }

    /**
     * @param Request                         $request
     * @param UserNotificationTokenRepository $notificationTokenRepository
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "token",
                    description: "Notification token",
                    type: "string",
                )]
        )
    )
    ]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: "Return when the user notification token is saved",
        content: new OA\JsonContent(properties: [
            new OA\Property(
                property: "error",
                type: "boolean",
                enum: [false]
            ),
            new OA\Property(
                property: "message",
                type: "string"
            ),
        ])
    )]
    #[Route(path: "/current/notification-tokens", name: "post_notification_tokens", methods: ["POST"])]
    public function notificationToken(Request $request, UserNotificationTokenRepository $notificationTokenRepository): JsonResponse
    {
        $user = $this->getUser();
        $content = $this->getContent($request);
        if (null !== $response = $this->checkParameter($content, 'token')) {
            return $response;
        }

        if (null === $notificationTokenRepository->findByToken($content['token'])) {
            /** @var UserNotificationToken $notificationToken */
            $notificationToken = $notificationTokenRepository->create();
            $notificationToken->setToken($content['token']);
            $notificationToken->setUser($user);

            $notificationTokenRepository->save($notificationToken);

            return $this->generateSuccessResponse(statusCode: Response::HTTP_CREATED);
        }

        return $this->generateSuccessResponse();
    }

    /**
     * Update user avatar
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\RequestBody(
        content: [new OA\MediaType(
                      mediaType: 'multipart/form-data',
                      schema: new OA\Schema(
                          properties: [
                              new OA\Property(
                                  property: "imageFile",
                                  description: "User avatar",
                                  type: "string",
                                  format: "binary"
                              ),
                          ]
                      )
                  )]
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: "Error response when parameter is missing",
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Response when current user image is successfully updated",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "error",
                    type: "boolean",
                    enum: [false]
                ),
                new OA\Property(
                    property: "message",
                    type: "string"
                ),]
        )
    )]
    #[Route(path: "/current/upload", name: "upload", methods: ["POST"])]
    public function upload(
        Request $request
    ): JsonResponse {
        $user = $this->getUser();
        $form = $this->createForm(AvatarFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->update($user);
            $this->userRepository->reload($user);
            $message = $this->translator->trans(
                'flash.success.user_avatar_updated',
                [],
                'application'
            );

            return $this->responseGenerator->generateSuccess(null, $message);
        }

        return $this->getMissingParamsResponse();
    }

    /**
     * Retrieve a user information
     *
     * @param integer|string    $idOrToken
     * @param UploaderHelper    $uploaderHelper
     * @param CompanyRepository $companyRepository
     * @return JsonResponse
     */
    #[OA\Parameter(
        name: "idOrToken",
        description: "User's id or token",
        in: "path",
        required: true,
        schema: new OA\Schema(oneOf: [
            new OA\Schema(type: "integer"),
            new OA\Schema(type: "string", format: "uuid"),
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: "Error response when user is not found",
        content: new Model(type: ErrorResponseData::class)
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Return when the user information is successfully retrieved",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "error",
                    type: "boolean",
                    enum: [false]
                ),
                new OA\Property(
                    property: "message",
                    type: "string"
                ),
                new OA\Property(
                    property: "data",
                    ref: new Model(type: UserDTO::class)
                )]
        )
    )]
    #[Route(path: "/{idOrToken}", name: "getc", methods: ["GET"])]
    public function getc(int|string $idOrToken, UploaderHelper $uploaderHelper, CompanyRepository $companyRepository): JsonResponse
    {
        /** @var CheckUserExistEvent $event */
        $event = $this->dispatchEvent(CheckUserExistEvent::class, $idOrToken);
        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $companies = $companyRepository->findByUser($event->getUser());

        return $this->generateSuccessResponse(UserBuilder::buildDTO($event->getUser(), $uploaderHelper, $companies));
    }
}
