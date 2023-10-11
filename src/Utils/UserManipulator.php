<?php

namespace App\Utils;

use App\Entity\InternalUser;
use App\Repository\InternalUserRepository;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

/**
 * Class UserManipulator
 *
 * @package App\Utils
 *
 * @author Vandeth Tho <thovandeth@gmail.com>
 */
class UserManipulator
{
    /**
     * @var InternalUserRepository
     */
    private InternalUserRepository $userRepository;

    /**
     * UserManipulator constructor.
     *
     * @param InternalUserRepository $userRepository
     */
    public function __construct(InternalUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Creates a user and returns it.
     *
     * @param string            $password
     * @param string            $firstName
     * @param string            $lastName
     * @param string            $email
     * @param string            $gender
     * @param DateTimeImmutable $dob
     * @param bool              $enabled
     * @param bool              $superAdmin
     *
     * @return InternalUser
     */
    public function create(
        string $password,
        string $firstName,
        string $lastName,
        string $email,
        string $gender,
        DateTimeImmutable $dob,
        bool $enabled,
        bool $superAdmin
    ): InternalUser {
        $user = $this->userRepository->create();
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setGender($gender);
        $user->setDob($dob);
        $user->setEnabled($enabled);
        $user->setSuperAdmin($superAdmin);
        $user->setToken(Uuid::v4()->__toString());

        $this->userRepository->updateUser($user);
        return $user;
    }

    /**
     * Activates the given user.
     *
     * @param string $email
     */
    public function activate(string $email): void
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setEnabled(true);
        $this->userRepository->update($user);
    }

    /**
     * Deactivates the given user.
     *
     * @param string $email
     */
    public function deactivate(string $email): void
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setEnabled(false);
        $this->userRepository->update($user);
    }

    /**
     * Changes the password for the given user.
     *
     * @param string $email
     * @param string $password
     *
     */
    public function changePassword(string $email, string $password): void
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setPlainPassword($password);
        $this->userRepository->updateUser($user);
    }

    /**
     * Promotes the given user.
     *
     * @param string $email
     *
     */
    public function promote(string $email): void
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setSuperAdmin(true);
        $this->userRepository->update($user);
    }

    /**
     * Demotes the given user.
     *
     * @param string $email
     */
    public function demote(string $email): void
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setSuperAdmin(false);
        $this->userRepository->update($user);
    }

    /**
     * Adds role to the given user.
     *
     * @param string $email
     * @param string $role
     *
     * @return bool true if role was added, false if user already had the role
     *
     */
    public function addRole(string $email, string $role): bool
    {
        $user = $this->findUserByEmailOrThrowException($email);
        if ($user->hasRole($role)) {
            return false;
        }
        $user->addRole($role);
        $this->userRepository->update($user);

        return true;
    }

    /**
     * Removes role from the given user.
     *
     * @param string $email
     * @param string $role
     *
     * @return bool true if role was removed, false if user didn't have the role
     */
    public function removeRole(string $email, string $role): bool
    {
        $user = $this->findUserByEmailOrThrowException($email);
        if (!$user->hasRole($role)) {
            return false;
        }
        $user->removeRole($role);
        $this->userRepository->update($user);

        return true;
    }

    /**
     * Finds a user by his email and throws an exception if we can't find it.
     *
     * @param string $email
     *
     * @return InternalUser
     */
    private function findUserByEmailOrThrowException(string $email): InternalUser
    {
        $user = $this->userRepository->findByIdentifier($email);

        if (!$user) {
            throw new InvalidArgumentException(sprintf('Product identified by "%s" email does not exist.', $email));
        }

        return $user;
    }
}
