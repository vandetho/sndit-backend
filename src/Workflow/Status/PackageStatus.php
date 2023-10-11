<?php

namespace App\Workflow\Status;

/**
 * Class PackageStatus
 *
 * @package App\Workflow\Status
 * @author Vandeth THO <thovandeth@gmail.com>
 */
final class PackageStatus
{
    public const NEW_PACKAGE = 'new_package';
    public const WAITING_FOR_DELIVERY = 'waiting_for_delivery';
    public const NOTIFY_DELIVERER = 'notify_deliverer';
    public const ON_DELIVERY = 'on_delivery';
    public const DELIVERED_NOTIFICATION = 'delivered_notification';
    public const DELIVERED = 'delivered';

}
