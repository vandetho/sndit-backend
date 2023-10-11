<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\InternalUser as InternalUserDTO;
use App\Entity\InternalUser;
use App\Utils\CanonicalFieldsUpdater;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class InternalUserRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method InternalUser      create()
 * @method InternalUserDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method InternalUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method InternalUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method InternalUser[]    findAll()
 * @method InternalUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InternalUserRepository extends AbstractRepository implements UserLoaderInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @var CanonicalFieldsUpdater
     */
    private CanonicalFieldsUpdater $canonicalFieldsUpdater;

    /**
     * InternalUserRepository constructor.
     *
     * @param ManagerRegistry             $registry
     * @param UserPasswordHasherInterface $passwordHasher
     * @param CanonicalFieldsUpdater      $canonicalFieldsUpdater
     */
    public function __construct(
        ManagerRegistry $registry,
        UserPasswordHasherInterface $passwordHasher,
        CanonicalFieldsUpdater $canonicalFieldsUpdater
    ) {
        parent::__construct($registry, InternalUser::class, InternalUserDTO::class);
        $this->passwordHasher = $passwordHasher;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * Find a user by his/her token
     *
     * @param string $token
     * @return InternalUser|null
     */
    public function findByToken(string $token): ?InternalUser
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @param array $criteria
     * @return InternalUser|null
     */
    public function findUserBy(array $criteria): ?InternalUser
    {
        return $this->findOneBy($criteria);
    }

    /**
     * @param string $email
     * @return InternalUser|null
     */
    public function findByEmail(string $email): ?InternalUser
    {
        return $this->findUserBy(['emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email)]);
    }

    /**
     * @param string $email
     * @return InternalUser|null
     */
    public function findByIdentifier(string $email): ?InternalUser
    {
        return $this->findByEmail($email);
    }

    /**
     * @param string $identifier
     * @return InternalUser|null
     */
    public function loadUserByIdentifier(string $identifier): ?InternalUser
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
     * @param InternalUser $user
     * @param bool         $andFlush
     */
    public function updateUser(InternalUser $user, bool $andFlush = true): void
    {
        $this->updateCanonicalFields($user);
        $this->hashPassword($user);
        $this->persist($user);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @param InternalUser $user
     * @return void
     */
    public function updateCanonicalFields(InternalUser $user): void
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    /**
     * @param InternalUser $user
     * @param bool         $andFlush
     */
    public function updatePassword(InternalUser $user, bool $andFlush = true): void
    {
        $this->updateUser($user, $andFlush);
    }

    /**
     * Hash user password
     *
     * @param InternalUser $user
     */
    private function hashPassword(InternalUser $user): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();
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
            u.email as email,
            u.emailCanonical as emailCanonical,
            u.countryCode as countryCode,
            u.firstName as firstName,
            u.lastName as lastName,
            u.gender as gender,
            u.telegramId as telegramId,
            u.token as token,
            u.imageName as imageFile,
            u.code as locale
        EOT;
    }
}
