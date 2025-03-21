<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
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

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['photo:read'])]
    private ?int $id = null;

    // #[Vich\UploadableField(mapping: 'photos', fileNameProperty: 'path', size: 'image.size')]
    /**
     * @Vich\UploadableField(mapping="photos", fileNameProperty="image.name", size="image.size")
     * @var File|null
     */
    #[Vich\UploadableField(mapping: 'photos', fileNameProperty: 'image.name', size: 'image.size')]
    // #[Groups(['photo:read', 'photo:write'])]
    private ?File $imageFile = null;

     /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     * @var EmbeddedFile|null
     */
    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    // #[Groups(['photo:read', 'photo:write'])]
    private ?EmbeddedFile $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $path = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $alt = null;

    #[ORM\Column(nullable: true)]
    private ?int $size = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $description = null;

    #[ORM\OneToOne(mappedBy: 'photo', cascade: ['persist', 'remove'])]
    #[Groups(['photo:read', 'photo:write'])]
    private ?Section $section = null;

    #[ORM\OneToOne(mappedBy: 'coverPhoto', cascade: ['persist', 'remove'])]
    #[Groups(['photo:read', 'photo:write'])]
    private ?Projets $projet = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return str_replace('public/', '', $this->path);
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
        return $this->image ? $this->image->getSize() : null;
    }
    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->image ? $this->image->getMimeType() : null;
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
            $this->projet = null; // Empêcher l'utilisation simultanée comme couverture de projet
        }

        // set the owning side of the relation if necessary
        if ($section !== null && $section->getPhoto() !== $this) {
            $section->setPhoto($this);
            $this->projet = null; // Empêcher l'utilisation simultanée comme couverture de projet
        }

        $this->section = $section;

        return $this;
    }

    public function getProjet(): ?Projets
    {
        return $this->projet;
    }

    public function setProjet(?Projets $projet): static
    {
        if ($projet !== null) {
            $this->section = null; // Empêcher l'utilisation simultanée dans une section
        }
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
        // Si l'image est null, l'initialiser
        if ($this->image === null) {
            $this->image = new EmbeddedFile();
        }
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

        if (null !== $imageFile) {
            // Log pour vérifier si l'upload est traité
            // dump('Image file received');
            // dump($imageFile);

            $this->updatedAt = new \DateTimeImmutable();

            if ($this->image === null) {
                $this->image = new EmbeddedFile();
            }

            // Remplir les informations de l'image
            $this->image->setName($imageFile->getBasename());
            $this->image->setMimeType($imageFile->getMimeType());
            $this->image->setSize($imageFile->getSize());

            // Log pour vérifier les données avant la persistance
            // dump('Image data to persist: ');
            // dump($this->image);
        }
    }



    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setImage(EmbeddedFile $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?EmbeddedFile
    {
        return $this->image;
    }

    public function getName(): ?string
    {
        return $this->image ? $this->image->getName() : null;
    }

    public function getOriginalName(): ?string
    {
        return $this->image ? $this->image->getOriginalName() : null;
    }

    public function getDimensions(): ?array
    {
        return $this->image ? $this->image->getDimensions() : null;
    }

    /**
     * Retourne le nom de l'image stockée dans l'objet embarqué.
     */
    #[Groups(['photo:read', 'photo:write'])]
    public function getImageName(): ?string
    {
        return $this->image ? $this->image->getName() : null;
    }

    // Vous pouvez aussi ajouter un getter pour l'original si besoin
    #[Groups(['photo:read', 'photo:write'])]
    public function getImageOriginalName(): ?string
    {
        return $this->image ? $this->image->getOriginalName() : null;
    }


}
