<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Ticket
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[Table(name: 'sndit_helpdesk_ticket')]
#[Entity(repositoryClass: TicketRepository::class)]
class Ticket extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[Column(name: 'name', type: Types::STRING, length: 150, nullable: false)]
    private ?string $name;

    /**
     * @var string|null
     */
    #[Column(name: 'token', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $token;

    /**
     * @var string|null
     */
    #[Column(name: 'email', type: Types::STRING, length: 150, nullable: true)]
    private ?string $email;

    /**
     * @var string|null
     */
    #[Column(name: 'phoneNumber', type: Types::STRING, length: 30, nullable: true)]
    private ?string $phoneNumber;

    /**
     * @var string|null
     */
    #[Column(name: 'content', type: Types::STRING, length: 255, nullable: false)]
    private ?string $content;

    /**
     * @var array
     */
    #[Column(name: 'marking', type: Types::JSON, nullable: false)]
    private array $marking = [];

    /**
     * @var User|null
     */
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user;

    /**
     * @var InternalUser|null
     */
    #[ManyToOne(targetEntity: InternalUser::class)]
    #[JoinColumn(name: 'internal_user_id', referencedColumnName: 'id')]
    private ?InternalUser $internalUser;

    /**
     * @var Collection
     */
    #[OneToMany(mappedBy: 'ticket', targetEntity: TicketAttachment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $files;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Ticket
     */
    public function setName(?string $name): Ticket
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return Ticket
     */
    public function setEmail(?string $email): Ticket
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return Ticket
     */
    public function setPhoneNumber(?string $phoneNumber): Ticket
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Ticket
     */
    public function setContent(?string $content): Ticket
    {
        $this->content = $content;

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
     * @return Ticket
     */
    public function setToken(?string $token): Ticket
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return array
     */
    public function getMarking(): array
    {
        return $this->marking;
    }

    /**
     * @param array $marking
     * @return Ticket
     */
    public function setMarking(array $marking): Ticket
    {
        $this->marking = $marking;

        return $this;
    }
    /**
     * Ticket constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
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
     * @return Ticket
     */
    public function setUser(?User $user): Ticket
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getFiles(): ArrayCollection|Collection
    {
        return $this->files;
    }

    /**
     * @param ArrayCollection|Collection $files
     * @return Ticket
     */
    public function setFiles(ArrayCollection|Collection $files): Ticket
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @param TicketAttachment $file
     * @return Ticket
     */
    public function addFile(TicketAttachment $file): Ticket
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setTicket($this);
        }

        return $this;
    }

    /**
     * @param TicketAttachment $file
     * @return Ticket
     */
    public function removeFile(TicketAttachment $file): Ticket
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            $file->setTicket(null);
        }

        return $this;
    }

    /**
     * @return InternalUser|null
     */
    public function getInternalUser(): ?InternalUser
    {
        return $this->internalUser;
    }

    /**
     * @param InternalUser|null $internalUser
     * @return Ticket
     */
    public function setInternalUser(?InternalUser $internalUser): Ticket
    {
        $this->internalUser = $internalUser;

        return $this;
    }
}
