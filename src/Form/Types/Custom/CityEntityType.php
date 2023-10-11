<?php
declare(strict_types=1);


namespace App\Form\Types\Custom;


use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CityEntityType
 *
 * @package App\Form\Types\Custom
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CityEntityType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => City::class
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return EntityType::class;
    }
}
