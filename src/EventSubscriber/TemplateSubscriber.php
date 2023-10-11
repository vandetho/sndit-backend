<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use App\Event\Template\CheckTemplateExistEvent;
use App\Event\Template\CreateTemplateEvent;
use App\Repository\TemplateRepository;
use App\Utils\ResponseGeneratorInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TemplateSubscriber
 *
 * @package App\EventSubscriber
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TemplateSubscriber extends AbstractSubscriber
{
    /**
     * @var TemplateRepository
     */
    private TemplateRepository $templateRepository;

    /**
     * TemplateSubscriber constructor.
     *
     * @param ResponseGeneratorInterface $responseGenerator
     * @param TranslatorInterface        $translator
     * @param Security                   $security
     * @param TemplateRepository         $templateRepository
     */
    public function __construct(
        ResponseGeneratorInterface $responseGenerator,
        TranslatorInterface $translator,
        Security $security,
        TemplateRepository $templateRepository,
    ) {
        parent::__construct($responseGenerator, $translator, $security);
        $this->templateRepository = $templateRepository;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        CheckTemplateExistEvent::class => "string",
        CreateTemplateEvent::class     => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            CheckTemplateExistEvent::class => 'onCheckExist',
            CreateTemplateEvent::class     => 'onCreate',
        ];
    }

    /**
     * @param CheckTemplateExistEvent $event
     * @return void
     */
    public function onCheckExist(CheckTemplateExistEvent $event): void
    {
        $id = $event->getId();
        if (null === $template = $this->templateRepository->find($id)) {
            $message = $this->translator->trans('flash.errors.template_not_found', ['%id%' => $id], 'application');
            $event->setResponse($this->responseGenerator->generateError($message, Response::HTTP_NOT_FOUND));

            return;
        }
        $event->setTemplate($template);
    }

    /**
     * @param CreateTemplateEvent $event
     * @return void
     */
    public function onCreate(CreateTemplateEvent $event): void
    {
        $package = $event->getPackage();
        $template = $this->templateRepository->create();
        $template->setName($package->getName());
        $template->setPhoneNumber($package->getPhoneNumber());
        $template->setAddress($package->getAddress());
        $template->setCity($package->getCity());
        $template->setCompany($package->getCompany());
        $template->setCreator($this->getUser());

        $this->templateRepository->save($template);
    }
}
