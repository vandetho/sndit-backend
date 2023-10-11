<?php
declare(strict_types=1);


namespace App\Builder;


use App\Entity\Employee;
use App\Entity\Package;

/**
 * Class PackageBuilder
 *
 * @package App\Builder
 * @author Vandeth THO <thovandeth@gmail.com>
 */
class PackageBuilder
{
    /**
     * @param Package       $package
     * @param Employee|null $employee
     * @return \App\DTO\Package
     */
    public static function buildDTO(Package $package, ?Employee $employee = null): \App\DTO\Package
    {
        $dto = new \App\DTO\Package();
        $dto->id = $package->getId();
        $dto->name = $package->getName();
        $dto->phoneNumber = $package->getPhoneNumber();
        $dto->address = $package->getAddress();
        $dto->token = $package->getToken();
        $dto->marking = $package->getMarking();
        $dto->note = $package->getNote();
        $dto->createdAt = $package->getCreatedAt();
        $dto->updatedAt = $package->getUpdatedAt();
        $dto->latitude = $package->getLatitude();
        $dto->longitude = $package->getLongitude();
        $dto->city = CityBuilder::buildDTO($package->getCity());
        $dto->creator = UserBuilder::buildDTO($package->getCreator());
        $dto->company = CompanyBuilder::buildDTO($package->getCompany(), $employee);
        if ($employee) {
            $dto->roles = $employee->getRoles();
        }
        if ($package->getUser()) {
            $dto->user =  UserBuilder::buildDTO($package->getUser());
        }
        if ($package->getDeliverer()) {
            $dto->deliverer =  UserBuilder::buildDTO($package->getDeliverer());
        }
        return $dto;
    }
}
