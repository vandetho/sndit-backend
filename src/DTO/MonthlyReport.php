<?php

namespace App\DTO;

/**
 * Class MonthlyReport
 *
 * @package App\DTO
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class MonthlyReport
{
    /**
     * @var string
     */
    public string $month;

    /**
     * @var string|null
     */
    public ?string $year = null;

    /**
     * @var string
     */
    public string $total;
}
