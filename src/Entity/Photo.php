<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'photos', fileNameProperty: 'path', size: 'size')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $path = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $alt = null;

    #[ORM\Column(nullable: false)]
    private ?int $size = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(mappedBy: 'photo', cascade: ['persist', 'remove'])]
    private ?Section $section = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): static
    {
        // unset the owning side of the relation if necessary
        if ($section === null && $this->section !== null) {
            $this->section->setPhoto(null);
        }

        // set the owning side of the relation if necessary
        if ($section !== null && $section->getPhoto() !== $this) {
            $section->setPhoto($this);
        }

        $this->section = $section;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Logic to manage user-specific directories
    public function getUploadDir(): string
    {
        // Vous pouvez utiliser l'ID de l'utilisateur ici pour créer un dossier unique
        return 'uploads/photos/' . $this->user->getId();
    }

    /**
     * Get the value of imageFile
     */ 
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Set the value of imageFile
     *
     * @return  self
     */ 
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
       
    }
}
