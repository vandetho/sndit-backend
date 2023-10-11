<?php

namespace App\Utils;


use App\Entity\InternalUser;

/**
 * Class updating the canonical fields of the user.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class CanonicalFieldsUpdater
{
    /**
     * @var CanonicalizerInterface
     */
    private CanonicalizerInterface $emailCanonicalizer;

    /**
     * CanonicalFieldsUpdater constructor.
     *
     * @param CanonicalizerInterface $emailCanonicalizer
     */
    public function __construct(CanonicalizerInterface $emailCanonicalizer)
    {
        $this->emailCanonicalizer = $emailCanonicalizer;
    }

    /**
     * Canonicalizes an email and username.
     *
     * @param InternalUser $user
     */
    public function updateCanonicalFields(InternalUser $user): void
    {
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    /**
     * Canonicalizes an email.
     *
     * @param string|null $email
     *
     * @return string|null
     */
    public function canonicalizeEmail(?string $email): ?string
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }
}
