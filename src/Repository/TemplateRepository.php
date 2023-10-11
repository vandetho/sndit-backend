<?php
declare(strict_types=1);


namespace App\Repository;


use App\DTO\Template as TemplateDTO;
use App\Entity\Template;
use App\Repository;
use App\Workflow\Status\EmployeeStatus;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TemplateRepository
 *
 * @package App
 * @author Vandeth THO <thovandeth@gmail.com>
 * @method Template      create()
 * @method TemplateDTO   findByCriteria(array $criteria, array $orderBy = [], int $offset = null, int $limit = null)
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template[]    findAll()
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateRepository extends Repository\AbstractRepository
{
    /**
     * TemplateRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class, TemplateDTO::class);
    }

    /**
     * @inheritDoc
     */
    public function findByCriteriaQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.company', 'c')
            ->innerJoin('c.employees', 'e', Join::WITH, 'e.user = :user AND '.sprintf("JSON_EXTRACT(e.marking, '$.%s') = 1", EmployeeStatus::ACTIVE))
            ->innerJoin('t.creator', 'cr')
            ->leftJoin('t.city', 'ct')
            ->where('c.id = :company');
    }

    /**
     * @inheritDoc
     */
    public function countByCriteriaQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.company', 'c')
            ->innerJoin('c.employees', 'e', Join::WITH, 'e.user = :user AND '.sprintf("JSON_EXTRACT(e.marking, '$.%s') = 1", EmployeeStatus::ACTIVE))
            ->where('t.company = :company');

        return $qb->select($qb->expr()->count('DISTINCT t.id'));
    }

    /**
     * @inheritDoc
     */
    protected function getSelect(): string
    {
        return <<<EOL
            DISTINCT t.id,
            t.name,
            t.phoneNumber,
            t.address,
            c.id as company_id,
            c.name as company_name,
            c.canonicalName as company_canonicalName,
            c.token as company_token,
            ct.id as city_id,
            ct.name as city_name,
            cr.id as creator_id,
            cr.firstName as creator_firstName,
            cr.lastName as creator_lastName,
            cr.phoneNumber as creator_phoneNumber
        EOL;
    }
}
