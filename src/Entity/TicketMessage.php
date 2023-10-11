<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\TicketMessageRepository;
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
#[Table(name: 'sndit_helpdesk_ticket_message')]
#[Entity(repositoryClass: TicketMessageRepository::class)]
class TicketMessage extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[Column(name: 'content', type: Types::STRING, length: 255, nullable: false)]
    private ?string $content;

    /**
     * @var User|null
     */
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    /**
     * @var InternalUser|null
     */
    #[ManyToOne(targetEntity: InternalUser::class)]
    #[JoinColumn(name: 'internal_user_id', referencedColumnName: 'id')]
    private ?InternalUser $internalUser = null;

    /**
     * @var Ticket|null
     */
    #[ManyToOne(targetEntity: Ticket::class, inversedBy: 'files')]
    #[JoinColumn(name: 'ticket_id', referencedColumnName: 'id', nullable: false)]
    private ?Ticket $ticket;

    /**
     * @var Collection<int, TicketAttachment>
     */
    #[OneToMany(mappedBy: 'message', targetEntity: TicketAttachment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attachments;

    /**
     * TicketMessage constructor.
     */
    public function __construct()
    {
        $this->attachments = new ArrayCollection();
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
     * @return TicketMessage
     */
    public function setContent(?string $content): TicketMessage
    {
        $this->content = $content;

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
     * @return TicketMessage
     */
    public function setUser(?User $user): TicketMessage
    {
        $this->user = $user;

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
     * @return TicketMessage
     */
    public function setInternalUser(?InternalUser $internalUser): TicketMessage
    {
        $this->internalUser = $internalUser;

        return $this;
    }

    /**
     * @return Ticket|null
     */
    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    /**
     * @param Ticket|null $ticket
     * @return TicketMessage
     */
    public function setTicket(?Ticket $ticket): TicketMessage
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return ArrayCollection<int, TicketMessage>|Collection<int, TicketMessage>
     */
    public function getAttachments(): ArrayCollection|Collection
    {
        return $this->attachments;
    }

    /**
     * @param ArrayCollection<int, TicketMessage>|Collection<int, TicketMessage> $attachments
     * @return TicketMessage
     */
    public function setAttachments(ArrayCollection|Collection $attachments): TicketMessage
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @param TicketAttachment $attachment
     * @return TicketMessage
     */
    public function addAttachment(TicketAttachment $attachment): TicketMessage
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setMessage($this);
        }

        return $this;
    }

    /**
     * @param TicketAttachment $attachment
     * @return TicketMessage
     */
    public function removeAttachment(TicketAttachment $attachment): TicketMessage
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            $attachment->setMessage(null);
        }

        return $this;
    }
}
