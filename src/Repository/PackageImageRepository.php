<?php
declare(strict_types=1);


namespace App\Repository;

use App\DTO\PackageImage as PackageImageDTO;
use App\Entity\Package;
use App\Entity\PackageHistory;
use App\Entity\PackageImage;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PackageImageRepository
 *
 * @package App\Repository
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method PackageImage      create()
 * @method PackageImageDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method PackageImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackageImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackageImage[]    findAll()
 * @method PackageImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackageImageRepository extends AbstractRepository
{
    /**
     * PackageImageRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackageImage::class, PackageImageDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('pi')
            ->where('pi.package = :package');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('pi');

        return $qb->select($qb->expr()->count('p'))
            ->where('pi.package = :package');
    }

    /**
     * @param Package[]|int[] $packages
     * @return PackageImage[]
     */
    public function findByPackages(array $packages): array
    {
        return $this->findBy(['package' => $packages]);
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOL
            pi.id,
            pi.imageName
        EOL;
    }
}
