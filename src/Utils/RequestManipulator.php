<?php
declare(strict_types=1);


namespace App\Utils;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestManipulator
 *
 * @package App\Utils
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class RequestManipulator
{
    /**
     * @param Request $request
     * @param bool    $assoc
     * @param int     $depth
     * @param int     $options
     * @return array
     */
    public static function getContent(Request $request, bool $assoc = true, int $depth = 512, int $options = JSON_THROW_ON_ERROR): array
    {
        return json_decode($request->getContent(), $assoc, $depth, $options);
    }

    /**
     * @param Request       $request
     * @param FormInterface $form
     * @return array
     */
    public static function getData(Request $request, FormInterface $form): array
    {
        return $request->getContentTypeFormat() === 'json' ? $request->toArray() : $request->request->all($form->getName() ?: null);
    }

}
