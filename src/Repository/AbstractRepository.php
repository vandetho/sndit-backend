<?php
declare(strict_types=1);


namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AbstractRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @template T
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    protected string $dtoClass;

    /**
     * AbstractRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param string          $entityClass
     * @param string          $dtoClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass, string $dtoClass)
    {
        parent::__construct($registry, $entityClass);
        $this->dtoClass = $dtoClass;
    }

    /**
     * @return T
     */
    public function create()
    {
        return new $this->_entityName;
    }

    /**
     * Save an object in the database
     *
     * @param T    $entity
     * @param bool $andFlush tell the manager whether the object need to be flush or not
     */
    public function save(mixed $entity, bool $andFlush = true): void
    {
        $this->persist($entity);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * Delete an object from the database
     *
     * @param T    $entity
     * @param bool $andFlush tell the manager whether the object need to be flush or not
     */
    public function delete(mixed $entity, bool $andFlush = true): void
    {
        $this->remove($entity);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @param T $entity
     * @param bool  $andFlush
     */
    public function update(mixed $entity, bool $andFlush = true): void
    {
        $this->persist($entity);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @param T $entity
     *
     * @return void
     */
    public function persist(mixed $entity): void
    {
        $this->_em->persist($entity);
    }

    /**
     * @param T $entity
     *
     * @return void
     */
    public function remove(mixed $entity): void
    {
        $this->_em->remove($entity);
    }

    /**
     * @param T $entity
     */
    public function reload(mixed $entity): void
    {
        $this->_em->refresh($entity);
    }

    /**
     * Flushes all changes to objects that have been queued up too now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->_em->flush();
    }

    /**
     * Find data by criteria
     *
     * @param array    $criteria
     * @param array    $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @return array
     */
    public function findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null): array
    {
        $qb = $this->findByCriteriaQuery()
            ->select($this->getSelect())
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters($criteria);

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        return $qb->getQuery()->getResult($this->dtoClass);
    }

    /**
     * Count total data by criteria
     *
     * @param array $criteria
     * @return integer
     * @throws NonUniqueResultException
     */
    public function countByCriteria(array $criteria): int
    {
        try {
            return $this->countByCriteriaQuery()
                ->setParameters($criteria)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * Find data by criteria in form of query
     *
     * @return QueryBuilder
     */
    abstract public function findByCriteriaQuery(): QueryBuilder;

    /**
     * Count data by criteria in form of query
     *
     * @return QueryBuilder
     */
    abstract public function countByCriteriaQuery(): QueryBuilder;

    /**
     * Select query
     *
     * @return string|array
     */
    abstract protected function getSelect(): string|array;
}
