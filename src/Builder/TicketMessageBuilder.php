<?php
declare(strict_types=1);


namespace App\Builder;


use App\Entity\TicketMessage;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class TicketBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketMessageBuilder
{
    /**
     * @param TicketMessage  $message
     * @param UploaderHelper $uploaderHelper
     * @return \App\DTO\TicketMessage
     */
    public static function buildDTO(TicketMessage $message, UploaderHelper $uploaderHelper): \App\DTO\TicketMessage
    {
        $dto = new \App\DTO\TicketMessage();
        $dto->id = $message->getId();
        $dto->content = $message->getContent();
        $dto->createdAt = $message->getCreatedAt();
        $dto->updatedAt = $message->getUpdatedAt();
        foreach ($message->getAttachments() as $attachment){
            $dto->attachments[] = $uploaderHelper->asset($attachment, 'file');
        }
        if ($message->getUser()){
            $dto->user = UserBuilder::buildDTO($message->getUser());
        }
        if ($message->getInternalUser()){
            $dto->internalUser = InternalUserBuilder::buildDTO($message->getInternalUser());
        }
        return $dto;
    }
}
