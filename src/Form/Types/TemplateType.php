<?php
declare(strict_types=1);


namespace App\Form\Types;


use App\Entity\Template;
use App\Form\Types\Custom\CityEntityType;
use App\Form\Types\Custom\CompanyEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TemplateType
 *
 * @package App\Form\Types
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TemplateType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [])
            ->add('phoneNumber', TextType::class, [])
            ->add('address', TextType::class, [])
            ->add('city', CityEntityType::class, [])
            ->add('company', CompanyEntityType::class, [])
            ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => Template::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return '';
    }

}
