<?php
declare(strict_types=1);

namespace App\Workflow;


use App\Entity\InternalUser;
use App\Entity\User;
use App\Utils\ResponseGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractWorkflow
 *
 * @package App\Workflow
 * @author Vandeth THO <thovandeth@gmail.com>
 */
abstract class AbstractWorkflow
{
    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var ResponseGeneratorInterface
     */
    protected ResponseGeneratorInterface $responseGenerator;

    /**
     * @var Security
     */
    protected Security $security;

    /**
     * AbstractResupplyWorkflow constructor.
     *
     * @param Security                       $security
     * @param TranslatorInterface            $translator
     * @param ResponseGeneratorInterface     $responseGenerator
     */
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        ResponseGeneratorInterface $responseGenerator
    ) {
        $this->translator = $translator;
        $this->responseGenerator = $responseGenerator;
        $this->security = $security;
    }


    /**
     * @return InternalUser|User|UserInterface
     */
    protected function getUser(): InternalUser|User|UserInterface
    {
        return $this->security->getUser();
    }

    /**
     * @param Request $request
     * @param bool    $assoc
     * @param int     $depth
     * @param int     $options
     * @return array
     */
    protected function getContent(Request $request, bool $assoc = true, int $depth = 512, int $options = JSON_THROW_ON_ERROR): array
    {
        return json_decode($request->getContent(), $assoc, $depth, $options);
    }
}
