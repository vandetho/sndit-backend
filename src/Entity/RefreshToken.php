<?php
declare(strict_types=1);



namespace App\Entity;


use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RefreshToken
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\Table(name: 'sndit_refresh_token')]
#[UniqueEntity(fields: ['refreshToken'])]
class RefreshToken extends AbstractEntity implements RefreshTokenInterface
{
    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[ORM\Column(name: 'refresh_token', type: Types::STRING, length: 128, unique: true, nullable: false)]
    private ?string $refreshToken = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[ORM\Column(name: 'username', type: Types::STRING, length: 255, nullable: false)]
    private ?string $username = null;

    /**
     * @var DateTime|null
     */
    #[Assert\NotBlank]
    #[ORM\Column(name: 'valid', type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?DateTime $valid;

    /**
     *
     * Creates a new model instance based on the provided details.
     * @param string        $refreshToken
     * @param UserInterface $user
     * @param int           $ttl
     * @return RefreshTokenInterface
     * @throws Exception
     */
    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): RefreshTokenInterface
    {
        $valid = new DateTime();
        $valid->modify('+'.$ttl.' seconds');

        $model = new static();
        $model->setRefreshToken($refreshToken);
        $model->setUsername($user->getUserIdentifier());
        $model->setValid($valid);

        return $model;
    }

    /**
     * Set refreshToken.
     *
     * @param string|null $refreshToken
     *
     * @return RefreshToken
     * @throws Exception
     */
    public function setRefreshToken($refreshToken = null): RefreshToken
    {
        if (null === $refreshToken || '' === $refreshToken) {
            trigger_deprecation('gesdinet/jwt-refresh-token-bundle', '1.0', 'Passing an empty token to %s() to automatically generate a token is deprecated.', __METHOD__);

            $refreshToken = bin2hex(random_bytes(64));
        }

        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * Get refreshToken.
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * Set valid.
     *
     * @param DateTime $valid
     *
     * @return RefreshToken
     */
    public function setValid($valid): RefreshToken
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid.
     *
     * @return DateTime|null
     */
    public function getValid(): ?DateTime
    {
        return $this->valid;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return RefreshToken
     */
    public function setUsername($username): RefreshToken
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Check if is a valid refresh token.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid >= new DateTime();
    }

    /**
     * @return string Refresh Token
     */
    #[Pure]
    public function __toString()
    {
        return $this->getRefreshToken() ?? '';
    }
}
