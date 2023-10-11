<?php
declare(strict_types=1);


namespace App\Utils;


use Symfony\Component\Form\FormInterface;

/**
 * Class FormManipulator
 *
 * @package App\Utils
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class FormManipulator
{
    /**
     * List all errors of a given bound form.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    public static function getFormErrors(FormInterface $form): array
    {
        $errors = array();

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        // Fields
        /** @var FormInterface $child */
        foreach ($form as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }
}
