<?php
declare(strict_types=1);


namespace App\Form\Types\Custom;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GenderType
 *
 * @package App\Form\Types\Custom
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class GenderType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices'  => [
                'form.choice.male' => 'M',
                'form.choice.female'  => 'F',
                'form.choice.unspecific'  => 'U',
            ],
            'multiple' => false,
            'expanded' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
