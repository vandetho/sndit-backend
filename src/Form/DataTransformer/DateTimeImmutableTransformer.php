<?php
declare(strict_types=1);


namespace App\Form\DataTransformer;


use DateTimeImmutable;
use Exception;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DateTimeImmutableTransformer
 *
 * @package App\Form\DataTransformer
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class DateTimeImmutableTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected string $displayFormat;

    /**
     * DateTimeTransformer constructor.
     *
     * @param string $displayFormat
     */
    public function __construct(string $displayFormat = 'd/m/Y')
    {
        $this->displayFormat = $displayFormat;
    }

    /**
     * @inheritDoc
     */
    public function transform($value): mixed
    {
        return $value === null ? $value : $value->format($this->displayFormat);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function reverseTransform($value): ?DateTimeImmutable
    {
        return $value === null ? $value : new DateTimeImmutable($value);
    }
}
