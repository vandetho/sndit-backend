<?php
declare(strict_types=1);

namespace App\Entity;

use App\Constants\Gender;
use App\Constants\UserRole;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class User
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'sndit_user')]
#[ORM\UniqueConstraint(name: 'unique_telegram_room', columns: ['telegram_id', 'token'])]
#[Vich\Uploadable]
class User extends AbstractEntity implements UserInterface
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'email', type: Types::STRING, unique: true, nullable: true)]
    private ?string $email;
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'email_canonical', type: Types::STRING, unique: true, nullable: true)]
    private ?string $emailCanonical;
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 30, unique: true, nullable: false)]
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
    private array $roles = [UserRole::ROLE_USER];

    /**
     * @var bool|null
     */
    #[ORM\Column(name: 'verified', type: Types::BOOLEAN, nullable: false)]
    private ?bool $verified = false;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(name: 'deleted_at', type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

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
     * @var OTP|null
     */
    #[ORM\ManyToOne(targetEntity: OTP::class)]
    #[ORM\JoinColumn(name: "last_otp_id", referencedColumnName: "id", nullable: true)]
    private ?OTP $lastOTP = null;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): User
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
     * @return $this
     */
    public function setEmailCanonical(?string $emailCanonical): User
    {
        $this->emailCanonical = $emailCanonical;
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
     * @return User
     */
    public function setPhoneNumber(?string $phoneNumber): User
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
        return (string)$this->phoneNumber;
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
     * @return User
     */
    public function setFirstName(?string $firstName): User
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
     * @return User
     */
    public function setLastLogin(?DateTimeImmutable $lastLogin): User
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTimeImmutable|null $deletedAt
     * @return User
     */
    public function setDeletedAt(?DateTimeImmutable $deletedAt): User
    {
        $this->deletedAt = $deletedAt;

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
     * @return User
     */
    public function setImageFile(?File $imageFile): User
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
     * @return User
     */
    public function setImageName(?string $imageName): User
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
     * @return User
     */
    public function setGender(?string $gender): User
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
     * @return User
     */
    public function setTelegramId(?string $telegramId): User
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
     * @return User
     */
    public function setToken(?string $token): User
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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
     * @return User
     */
    public function setLastName(?string $lastName): User
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
     * @return User
     */
    public function setDob(?DateTimeImmutable $dob): User
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
     * @return User
     */
    public function setLocale(?string $locale): User
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return OTP|null
     */
    public function getLastOTP(): ?OTP
    {
        return $this->lastOTP;
    }

    /**
     * @param OTP|null $lastOTP
     * @return User
     */
    public function setLastOTP(?OTP $lastOTP): User
    {
        $this->lastOTP = $lastOTP;

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
    }

    /**
     * @return bool|null
     */
    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    /**
     * @param bool|null $verified
     * @return User
     */
    public function setVerified(?bool $verified): User
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleting(): bool
    {
        return !is_null($this->getDeletedAt());
    }
}
