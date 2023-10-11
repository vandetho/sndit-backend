<?php
declare(strict_types=1);


namespace App\Entity;


use App\Repository\PackageImageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class PackageImage
 *
 * @package App\Entity
 * @author Vandeth THO <thovandeth@gmail.com>
 */
#[ORM\Entity(repositoryClass: PackageImageRepository::class)]
#[ORM\Table(name: 'sndit_package_image')]
#[Vich\Uploadable]
class PackageImage extends AbstractEntity
{
    /**
     * @var File|null
     */
    #[Vich\UploadableField(mapping: 'packages', fileNameProperty: 'imageName')]
    private File|null $imageFile = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'image_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $imageName = null;

    /**
     * @var Package|null
     */
    #[ORM\ManyToOne(targetEntity: Package::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'package_id', referencedColumnName: 'id', nullable: false)]
    private ?Package $package = null;

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return PackageImage
     */
    public function setImageFile(?File $imageFile): PackageImage
    {
        $this->imageFile = $imageFile;
        if ($imageFile !== null) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string|null $imageName
     * @return PackageImage
     */
    public function setImageName(?string $imageName): PackageImage
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return Package|null
     */
    public function getPackage(): ?Package
    {
        return $this->package;
    }

    /**
     * @param Package|null $package
     * @return PackageImage
     */
    public function setPackage(?Package $package): PackageImage
    {
        $this->package = $package;

        return $this;
    }
}
