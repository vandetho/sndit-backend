<?php

namespace App\Form\Types;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TicketAttachmentsType
 *
 * @package App\Form\Types
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class TicketAttachmentsType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('images', CollectionType::class, [
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add'    => true,
            'entry_type'   => TicketAttachmentType::class,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => Ticket::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return 'sndit_ticket_attachments';
    }

}
