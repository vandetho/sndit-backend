<?php
declare(strict_types=1);

namespace App\Entity;

use App\Constants\Gender;
use App\Constants\UserRole;
use App\Repository\InternalUserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class User
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: InternalUserRepository::class)]
#[ORM\Table(name: 'sndit_internal_user')]
#[ORM\UniqueConstraint(name: 'unique_telegram_room', columns: ['telegram_id', 'token'])]
#[Vich\Uploadable]
class InternalUser extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'email', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $email = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'email_canonical', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $emailCanonical = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'password', type: Types::STRING, length: 255, nullable: false)]
    private ?string $password = null;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'reset_password', type: Types::BOOLEAN, nullable: false)]
    private bool $resetPassword = false;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, nullable: false)]
    private bool $enabled;

    /**
     * @var string|null
     */
    private ?string $plainPassword = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 30, unique: true, nullable: true)]
    private ?string $phoneNumber;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'country_code', type: Types::STRING, length: 5, nullable: true)]
    private ?string $countryCode;

    /**
     * @var array|string[]
     */
    #[ORM\Column(name: 'roles', type: Types::JSON, nullable: false)]
    private array $roles = [UserRole::ROLE_EMPLOYEE];

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 150, nullable: true)]
    private ?string $firstName = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 150, nullable: true)]
    private ?string $lastName = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(name: 'dob', type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $dob = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(name: 'last_login', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $lastLogin = null;

    /**
     * @var File|null
     */
    #[Vich\UploadableField(mapping: 'users', fileNameProperty: 'imageName')]
    private File|null $imageFile = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'image_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $imageName = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'gender', type: Types::STRING, length: 1, nullable: false)]
    private ?string $gender = Gender::UNSPECIFIED;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'telegram_id', type: Types::INTEGER, unique: true, nullable: true)]
    private ?string $telegramId = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'token', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $token = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'locale', type: Types::STRING, length: 10, nullable: false)]
    private ?string $locale = 'en';

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return InternalUser
     */
    public function setEmail(?string $email): InternalUser
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    /**
     * @param string|null $emailCanonical
     * @return InternalUser
     */
    public function setEmailCanonical(?string $emailCanonical): InternalUser
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return InternalUser
     */
    public function setPassword(?string $password): InternalUser
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return InternalUser
     */
    public function setEnabled(bool $enabled): InternalUser
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isResetPassword(): bool
    {
        return $this->resetPassword;
    }

    /**
     * @param bool $resetPassword
     * @return InternalUser
     */
    public function setResetPassword(bool $resetPassword): InternalUser
    {
        $this->resetPassword = $resetPassword;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     * @return InternalUser
     */
    public function setPlainPassword(?string $plainPassword): InternalUser
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return "+$this->phoneNumber";
    }

    /**
     * @return string
     */
    public function getSanitizePhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return InternalUser
     */
    public function setPhoneNumber(?string $phoneNumber): InternalUser
    {
        $sanitizedPhoneNumber = $this->sanitizePhoneNumber($phoneNumber);
        $this->validatePhoneNumber($sanitizedPhoneNumber);
        $this->phoneNumber = $sanitizedPhoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->emailCanonical;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return InternalUser
     */
    public function setFirstName(?string $firstName): InternalUser
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getLastLogin(): ?DateTimeImmutable
    {
        return $this->lastLogin;
    }

    /**
     * @param DateTimeImmutable|null $lastLogin
     * @return InternalUser
     */
    public function setLastLogin(?DateTimeImmutable $lastLogin): InternalUser
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return InternalUser
     */
    public function setImageFile(?File $imageFile): InternalUser
    {
        $this->imageFile = $imageFile;
        if ($imageFile !== null) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string|null $imageName
     * @return InternalUser
     */
    public function setImageName(?string $imageName): InternalUser
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     * @return InternalUser
     */
    public function setGender(?string $gender): InternalUser
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelegramId(): ?string
    {
        return $this->telegramId;
    }

    /**
     * @param string|null $telegramId
     * @return InternalUser
     */
    public function setTelegramId(?string $telegramId): InternalUser
    {
        $this->telegramId = $telegramId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return InternalUser
     */
    public function setToken(?string $token): InternalUser
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = UserRole::ROLE_EMPLOYEE;

        return array_unique($roles);
    }

    /**
     * @param string $role
     * @return InternalUser
     */
    public function addRole(string $role): InternalUser
    {
        $role = strtoupper($role);
        if ($role === UserRole::ROLE_EMPLOYEE) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function removeRole(string $role): self
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ROLE_ADMIN);
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::ROLE_SUPER_ADMIN);
    }

    /**
     * @param bool $boolean
     * @return InternalUser
     */
    public function setSuperAdmin(bool $boolean): InternalUser
    {
        if (true === $boolean) {
            $this->addRole(UserRole::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(UserRole::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @param array $roles
     * @return InternalUser
     */
    public function setRoles(array $roles): InternalUser
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return InternalUser
     */
    public function setLastName(?string $lastName): InternalUser
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDob(): ?DateTimeImmutable
    {
        return $this->dob;
    }

    /**
     * @param DateTimeImmutable|null $dob
     * @return InternalUser
     */
    public function setDob(?DateTimeImmutable $dob): InternalUser
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     * @return InternalUser
     */
    public function setLocale(?string $locale): InternalUser
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param string $phoneNumber
     * @return string
     */
    private function sanitizePhoneNumber(string $phoneNumber): string
    {
        return str_replace('+', '', $phoneNumber);
    }

    /**
     * @param string $phoneNumber
     * @return void
     */
    private function validatePhoneNumber(string $phoneNumber): void
    {
        if (!preg_match('/^(?:\d ?){6,14}\d$/', $phoneNumber)) {
            throw new InvalidArgumentException(
                'Please provide phone number in E164 format without the \'+\' symbol'
            );
        }
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->getLastName()} {$this->getFirstName()}";
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
