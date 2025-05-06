<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\ProjetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Metadata\ApiProperty;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

// ROLE_USER
#[ORM\Entity(repositoryClass: ProjetsRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_USER')",
    normalizationContext: ['groups' => ['projet:read']],
    denormalizationContext: ['groups' => ['projet:write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['projet:read']]),
        new GetCollection(normalizationContext: ['groups' => ['projet:read']]),
        new Patch(
            uriTemplate: '/projects/{id}/cover',
            denormalizationContext: ['groups' => ['projet:cover:write']],
            inputFormats: ['multipart' => ['multipart/form-data']],
        ),
        new Put(),
        new Delete(),
    ],
    order: ['id' => 'ASC'],
    paginationEnabled: false
)]
class Projets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['projet:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['projet:read', 'projet:write'])]
    private ?string $title = null;

    /**
     * @var Collection<int, Section>
     */
    #[ORM\OneToMany(mappedBy: 'projets', targetEntity: Section::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['projet:read', 'projet:write'])]
    private Collection $sections;

    #[ORM\ManyToOne(inversedBy: 'projet')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['projet:read', 'projet:write'])]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options:["default" => "CURRENT_TIMESTAMP"])]
    #[Groups(['projet:read', 'projet:write'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options:["default" => "CURRENT_TIMESTAMP"])]
    #[Groups(['projet:read', 'projet:write'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['projet:read', 'projet:write'])]
    private ?Photo $coverPhoto = null;

    /**
     * Propriété “virtuelle” pour VichUploader
     * - mapping="project_cover" ➔ votre config vich_uploader.yaml
     * - fileNameProperty="coverName" ➔ champ string qui stocke le nom de fichier
     */
    #[Vich\UploadableField(mapping: 'project_cover', fileNameProperty: 'coverName')]
    #[ApiProperty(openapiContext: ['type' => 'string', 'format' => 'binary'])]
    #[Groups(['projet:cover:write'])]
    private ?File $coverFile = null;

    #[Groups(['projet:read'])]
    private ?string $coverName = null;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setProjets($this);
        }

        return $this;
    }

    public function removeSection(Section $section): static
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getProjets() === $this) {
                $section->setProjets(null);
            }
        }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCoverPhoto(): ?Photo
    {
        return $this->coverPhoto;
    }

    public function setCoverPhoto(?Photo $coverPhoto): static
    {
        if ($coverPhoto !== null) {
            $coverPhoto->setProjet($this);
        }

        $this->coverPhoto = $coverPhoto;
        return $this;
    }


    /**
     * Get the value of coverFile
     */ 
    public function getCoverFile()
    {
        return $this->coverFile;
    }

    /**
     * Set the value of coverFile
     *
     * @return  self
     */ 
    public function setCoverFile($coverFile)
    {
        $this->coverFile = $coverFile;

        return $this;
    }

    /**
     * Get the value of coverName
     */ 
    public function getCoverName()
    {
        return $this->coverName;
    }

    /**
     * Set the value of coverName
     *
     * @return  self
     */ 
    public function setCoverName($coverName)
    {
        $this->coverName = $coverName;

        return $this;
    }
}

