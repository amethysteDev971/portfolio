<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    normalizationContext: ['groups' => ['photo:read']],
    denormalizationContext: ['groups' => ['photo:write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['photo:read']]),
        new GetCollection(normalizationContext: ['groups' => ['photo:read']])
    ],
    order: ['id' => 'ASC'],
    paginationEnabled: false
)]
class Photo
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['photo:read'])]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'photos', fileNameProperty: 'path', size: 'size')]
    #[Groups(['photo:write'])]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?int $size = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $mimeType = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private ?\Vich\UploaderBundle\Entity\File $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['photo:read'])]
    private ?string $path = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $alt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(mappedBy: 'photo', cascade: ['persist', 'remove'])]
    private ?Section $section = null;

    #[ORM\OneToOne(mappedBy: 'coverPhoto', cascade: ['persist', 'remove'])]
    private ?Projets $projet = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setImageFile(?File $file = null): void
    {
        $this->imageFile = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): static
    {
        $this->path = $path;
        return $this;
    }

    /**
     * URL publique de l’image, construite par VichUploader
     */
    #[ApiProperty(readable: true)]
    #[Groups(['photo:read'])]
    #[SerializedName('url')]
    public function getUrl(): ?string
    {
        if (null === $this->path || null === $this->user) {
            return null;
        }

        return sprintf(
            '/uploads/photos/%d/%s',
            $this->user->getId(),
            $this->path
        );
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): static
    {
        $this->section = $section;
        return $this;
    }

    public function getProjet(): ?Projets
    {
        return $this->projet;
    }

    public function setProjet(?Projets $projet): static
    {
        $this->projet = $projet;
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

    /**
     * Get the value of size
     */ 
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the value of size
     *
     * @return  self
     */ 
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of mimeType
     */ 
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set the value of mimeType
     *
     * @return  self
     */ 
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }
}
