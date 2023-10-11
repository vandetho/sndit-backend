<?php
declare(strict_types=1);


namespace App\Workflow\Transition;


/**
 * Class TicketTransition
 *
 * @package App\Workflow\Transition
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class TicketTransition
{
    public const SUBMIT = 'submit';
    public const TREAT = 'treat';
    public const REJECT = 'reject';
    public const NEED_FEEDBACK = 'need_feedback';
    public const SUBMIT_FEEDBACK = 'submit_feedback';
    public const SOLVE = 'solve';
    public const CLOSE = 'close';
    public const CLOSE_SOLVED = 'close_solved';
}
