<?php
declare(strict_types=1);


namespace App\Hydrators;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator as BaseAbstractHydrator;
use Exception;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;

/**
 * Class AbstractHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
abstract class AbstractHydrator extends BaseAbstractHydrator
{
    /**
     * @var PropertyAccessor
     */
    protected PropertyAccessor $propertyAccessor;

    /**
     * @var PropertyInfoExtractor
     */
    protected PropertyInfoExtractor $propertyInfo;

    /**
     * @var string|null
     */
    protected ?string $dtoClass;

    /**
     * AbstractHydrator constructor.
     *
     * @param EntityManagerInterface $em
     * @param string|null            $dtoClass
     */
    public function __construct(EntityManagerInterface $em, ?string $dtoClass = null)
    {
        parent::__construct($em);
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicMethods()
            ->getPropertyAccessor();
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $listExtractors = [$reflectionExtractor];
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
        $descriptionExtractors = [$phpDocExtractor];
        $accessExtractors = [$reflectionExtractor];
        $propertyInitializableExtractors = [$reflectionExtractor];
        $this->propertyInfo = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );
        $this->dtoClass = $dtoClass;
    }

    /**
     * Find an object in an array
     *
     * @param array    $haystack
     * @param int|null $id
     *
     * @return false|int|string
     */
    protected function findById(array $haystack, ?int $id): bool|int|string
    {
        foreach ($haystack as $key => $value) {
            if ($value->getId() === $id) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return mixed
     * @throws Exception
     */
    protected function getValue(string $key, mixed $value): mixed
    {
        $finalValue = $value;
        if ($this->_rsm->typeMappings[$key] === 'datetime' || $this->_rsm->typeMappings[$key] === 'date') {
            $finalValue = new DateTime($value);
        }

        return $finalValue;
    }


    /**
     * @inheritDoc
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function hydrateAllData(): array
    {
        $results = [];
        foreach($this->_stmt->fetchAllAssociative() as $row) {
            $this->hydrateRowData($row, $results);
        }

        return $results;
    }


    /**
     * @param array $row
     * @param array $result
     * @return void
     */
    protected function hydrateRowData(array $row, array &$result): void
    {
        $dto = new $this->dtoClass();
        foreach ($row as $key => $value) {
            if (null !== $finalValue = $value) {
                $properties = explode('_', $this->_rsm->getScalarAlias($key));
                if (count($properties) > 0) {
                    if (count($properties) === 1) {
                        $this->propertyAccessor->setValue($dto, $properties[0], $finalValue);
                        continue;
                    }
                    $alias = [];
                    $path = '';
                    foreach ($properties as $property) {
                        $alias[] = $property;
                        $path = implode('.', $alias);
                        $type = $this->propertyInfo->getTypes($this->dtoClass, $path);
                        if (is_array($type)
                            && isset($type[0])
                            && $type[0]->getBuiltinType() === Type::BUILTIN_TYPE_OBJECT
                            && $this->propertyAccessor->getValue($dto, $path) === null
                        ) {
                            $class = $type[0]->getClassName();
                            $this->propertyAccessor->setValue($dto, $path, new $class());
                        }
                    }
                    $this->propertyAccessor->setValue($dto, $path, $finalValue);
                }
            }
        }
        $result[] = $dto;
    }
}
