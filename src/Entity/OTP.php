<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\OTPRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class OTP
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: OTPRepository::class)]
#[ORM\Table(name: 'sndit_otp')]
class OTP extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'phone_number', type: 'string', length: 180, nullable: false)]
    private ?string $phoneNumber = null;

    /**
     * @var string|null The OTP request_id
     */
    #[ORM\Column(name: "requestId", type: Types::STRING, unique: true, nullable: false)]
    private ?string $requestId = null;

    /**
     * @var string|null OTP price
     */
    #[ORM\Column(name: "price", type: Types::DECIMAL, precision: 20, scale: 6, nullable: true)]
    private ?string $price = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return OTP
     */
    public function setPhoneNumber(?string $phoneNumber): OTP
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /**
     * @param string|null $requestId
     * @return OTP
     */
    public function setRequestId(?string $requestId): OTP
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string|null $price
     * @return OTP
     */
    public function setPrice(?string $price): OTP
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return OTP
     */
    public function setUser(?User $user): OTP
    {
        $this->user = $user;

        return $this;
    }
}
