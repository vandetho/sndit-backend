<?php
declare(strict_types=1);


namespace App\Form\Types;


use App\Entity\User;
use App\Form\Types\Custom\CityEntityType;
use App\Form\Types\Custom\DateTimeType;
use App\Form\Types\Custom\GenderType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 *
 * @package App\Form\Types
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('gender', GenderType::class)
            ->add('dob', DateTimeType::class)
            ->add('imageFile', FileType::class)
            ->add('city', CityEntityType::class)
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => User::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): ?string
    {
        return '';
    }

}
