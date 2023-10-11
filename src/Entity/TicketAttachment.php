<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\TicketAttachmentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

/**
 * Class TicketAttachment
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[Table(name: 'sndit_helpdesk_ticket_attachment')]
#[Entity(repositoryClass: TicketAttachmentRepository::class)]
#[Uploadable]
class TicketAttachment extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[Column(name: 'filename', type: Types::STRING, length: 255, nullable: false)]
    private ?string $filename;

    /**
     * @var File|null
     */
    #[UploadableField(mapping: 'sndit_helpdesk_ticket_attachment', fileNameProperty: 'filename')]
    private ?File $file;

    /**
     * @var Ticket|null
     */
    #[ManyToOne(targetEntity: Ticket::class, inversedBy: 'files')]
    #[JoinColumn(name: 'ticket_id', referencedColumnName: 'id', nullable: false)]
    private ?Ticket $ticket;

    /**
     * @var TicketMessage|null
     */
    #[ManyToOne(targetEntity: TicketMessage::class, inversedBy: 'attachments')]
    #[JoinColumn(name: 'ticket_message_id', referencedColumnName: 'id', nullable: true)]
    private ?TicketMessage $message;

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return TicketAttachment
     */
    public function setFilename(?string $filename): TicketAttachment
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $file
     * @return TicketAttachment
     */
    public function setFile(?File $file = null): TicketAttachment
    {
        $this->file = $file;
        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
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
     * @return TicketAttachment
     */
    public function setTicket(?Ticket $ticket): TicketAttachment
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return TicketMessage|null
     */
    public function getMessage(): ?TicketMessage
    {
        return $this->message;
    }

    /**
     * @param TicketMessage|null $message
     * @return TicketAttachment
     */
    public function setMessage(?TicketMessage $message): TicketAttachment
    {
        $this->message = $message;

        return $this;
    }
}
