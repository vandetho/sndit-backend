<?php
declare(strict_types=1);


namespace App\Repository;


use App\DTO\OTP as OtpDTO;
use App\Entity\OTP;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class OTPRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method OTP      create()
 * @method OtpDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method OTP|null find($id, $lockMode = null, $lockVersion = null)
 * @method OTP|null findOneBy(array $criteria, array $orderBy = null)
 * @method OTP[]    findAll()
 * @method OTP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OTPRepository extends AbstractRepository
{
    /**
     * OTPRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OTP::class, OtpDTO::class);
    }


    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('otp')
            ->innerJoin('otp.user', 'u');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('otp');

        return $qb->select($qb->expr()->count('otp.id'))
            ->innerJoin('otp.user', 'u');
    }

    protected function getSelect(): string
    {
        return <<<EOT
            opt.id as id,
            otp.phoneNumber as phoneNumber,
            otp.requestId as requestId,
            otp.price as price
        EOT;
    }
}
