<?php
declare(strict_types=1);


namespace App\Form\Types;


use App\Entity\Package;
use App\Form\Types\Custom\BooleanType;
use App\Form\Types\Custom\CityEntityType;
use App\Form\Types\Custom\CompanyEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PackageType
 *
 * @package App\Form\Types
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [])
            ->add('phoneNumber', TextType::class, [])
            ->add('note', TextType::class, [])
            ->add('address', TextType::class, [])
            ->add('city', CityEntityType::class, [])
            ->add('company', CompanyEntityType::class, [])
            ->add('createTemplate', BooleanType::class, [
                'mapped' => false
            ])
            ->add('images', CollectionType::class, [
                'entry_type'   => PackageImageType::class,
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add'    => true,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => Package::class,
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
