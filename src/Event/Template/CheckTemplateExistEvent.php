<?php
declare(strict_types=1);


namespace App\Event\Template;


use App\Entity\Template;
use App\Event\AbstractEvent;

/**
 * Class CheckTemplateExistEvent
 *
 * @package App\Event\Template
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CheckTemplateExistEvent extends AbstractEvent
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var Template
     */
    private Template $template;

    /**
     * CheckTemplateExistEvent constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Template
     */
    public function getTemplate(): Template
    {
        return $this->template;
    }

    /**
     * @param Template $template
     * @return CheckTemplateExistEvent
     */
    public function setTemplate(Template $template): CheckTemplateExistEvent
    {
        $this->template = $template;

        return $this;
    }
}
