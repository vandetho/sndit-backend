<?php
declare(strict_types=1);


namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class BooleanTransformer
 *
 * @package App\Form\DataTransformer
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class BooleanTransformer implements DataTransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($value): mixed
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value): mixed
    {
        return $value === null ? false : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
