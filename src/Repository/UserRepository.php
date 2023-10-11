<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\User as UserDTO;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * Class UserRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method User      create()
 * @method UserDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository implements UserLoaderInterface
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class, UserDTO::class);
    }

    /**
     * Find a user by his/her token
     *
     * @param string $token
     * @return User|null
     */
    public function findByToken(string $token): ?User
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @param string $phoneNumber
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByIdentifier(string $phoneNumber): ?User
    {
        return $this->findByPhoneNumber($phoneNumber);
    }

    /**
     * @param string $phoneNumber
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByPhoneNumber(string $phoneNumber): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.phoneNumber LIKE :phoneNumber')
            ->setParameters([
                'phoneNumber' => str_replace('+', '', $phoneNumber),
            ])
            ->getQuery()->getOneOrNullResult();
    }


    /**
     * @param string $phoneNumber
     * @return array
     */
    public function findDeleteByPhoneNumber(string $phoneNumber): array
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.phoneNumber LIKE :phoneNumber')
            ->setParameters([
                'phoneNumber' => str_replace('+', '', $phoneNumber),
            ])
            ->getQuery()->getResult();
    }

    /**
     * @param string $identifier
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $identifier): ?User
    {
        return $this->findByIdentifier($identifier);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('u');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select($qb->expr()->count('u.id'));
    }

    /**
     * @return User[]
     */
    public function findAllDeleted(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.deletedAt = :date')
            ->setParameters(new ArrayCollection([
                new Parameter('date', date('Y-m-d')),
            ]))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @param bool $andFlush
     */
    public function updateUser(User $user, bool $andFlush = true): void
    {
        $this->persist($user);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @param array    $criteria
     * @param array    $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @return UserDTO[]
     */
    public function findDeletedByCriteria(array $criteria, array $orderBy, int $offset = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select($this->getSelect())
            ->where('u.deletedAt IS NOT NULL')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters($criteria);

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        return $qb->getQuery()->getResult($this->dtoClass);
    }

    /**
     * @param array $criteria
     * @return int
     * @throws NonUniqueResultException
     */
    public function countDeletedByCriteria(array $criteria): int
    {
        try {
            $qb = $this->createQueryBuilder('u')
                ->where('u.deletedAt IS NOT NULL')
                ->setParameters($criteria);

            return $qb->select($qb->expr()->count('u.id'))
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    /**
     * @return string
     */
    protected function getSelect(): string
    {
        return <<<EOT
            u.id as id,
            u.dob as dob,
            u.phoneNumber as phoneNumber,
            u.countryCode as countryCode,
            u.firstName as firstName,
            u.lastName as lastName,
            u.gender as gender,
            u.deletedAt as deletedAt,
            u.telegramId as telegramId,
            u.token as token,
            u.imageName as imageFile,
            u.code as locale
        EOT;
    }
}
