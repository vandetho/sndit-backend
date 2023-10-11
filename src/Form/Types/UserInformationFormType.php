<?php
declare(strict_types=1);



namespace App\Form\Types;


use App\Entity\User;
use App\Form\Types\Custom\DateTimeImmutableType;
use App\Form\Types\Custom\GenderType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserInformationFormType
 *
 * @package App\Form
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class UserInformationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phoneNumber', TextType::class, [])
            ->add('countryCode', TextType::class, [])
            ->add('firstName', TextType::class, [])
            ->add('lastName', TextType::class, [])
            ->add('dob', DateTimeImmutableType::class, [])
            ->add('gender', GenderType::class, [])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
