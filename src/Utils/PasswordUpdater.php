<?php

namespace App\Utils;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Class updating the hashed password in the user when there is a new password.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class PasswordUpdater implements PasswordUpdaterInterface
{
    /**
     * @var PasswordHasherFactoryInterface
     */
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    /**
     * PasswordUpdater constructor.
     *
     * @param PasswordHasherFactoryInterface $passwordHasherFactory
     */
    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
    }

    /**
     * @param User $user
     */
    public function hashPassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if ($plainPassword === '') {
            return;
        }

        $encoder = $this->passwordHasherFactory->getPasswordHasher($user);

        $hashedPassword = $encoder->hash($plainPassword);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }
}
