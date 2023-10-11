<?php
declare(strict_types=1);


namespace App\Form\Types\Custom;

use App\Form\DataTransformer\DateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DateTimeType
 *
 * @package App\Form\Types\Custom
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class DateTimeType extends AbstractType
{
    /**
     * @var DateTimeTransformer
     */
    private DateTimeTransformer $transformer;

    /**
     * DateTimeType constructor.
     *
     * @param DateTimeTransformer $transformer
     */
    public function __construct(DateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    /**
     * @inheritDoc
     */
    public function getParent(): string
    {
        return TextType::class;
    }
}
