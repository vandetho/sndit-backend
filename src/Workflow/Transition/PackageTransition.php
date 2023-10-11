<?php

namespace App\Workflow\Transition;

/**
 * Class PackageTransition
 *
 * @package App\Workflow\Transition
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class PackageTransition
{
    public const CREATE_NEW_PACKAGE = 'create_new_package';
    public const GIVE_TO_DELIVERER = 'give_to_deliverer';
    public const DELIVERER_NOTIFIED = 'deliverer_notified';
    public const TAKE_PACKAGE = 'take_package';
    public const DELIVER = 'deliver';
    public const DONE = 'done';
}
