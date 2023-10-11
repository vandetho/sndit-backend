<?php
declare(strict_types=1);


namespace App\Hydrators;


use App\DTO\OTP;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class OTPHydrator
 *
 * @package App\Hydrators
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class OTPHydrator extends AbstractHydrator
{
    /**
     * OTPHydrator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, OTP::class);
    }
}
