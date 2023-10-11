<?php
declare(strict_types=1);


namespace App\Form\Types\Custom;


use App\Constants\EmployeeRole;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Workflow\Status\EmployeeStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CompanyEntityType
 *
 * @package App\Form\Types\Custom
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class CompanyEntityType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'              => Company::class,
            'query_builder'      => function (CompanyRepository $companyRepository) {
                return $companyRepository->createQueryBuilder('c')
                    ->innerJoin('c.employees', 'e')
                    ->where('e.user = :user')
                    ->andWhere(sprintf("JSON_EXTRACT(e.marking, '$.%s') = 1", EmployeeStatus::ACTIVE))
                    ->andWhere(sprintf("JSON_SEARCH(e.roles, 'one',  '%s') IS NOT NULL", EmployeeRole::ROLE_MANAGER))
                    ->setParameters(['user' => $this->getUser()]);
            },
            'invalid_message'    => 'flash.errors.part_company_or_manager',
            'translation_domain' => 'application',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return EntityType::class;
    }

}
