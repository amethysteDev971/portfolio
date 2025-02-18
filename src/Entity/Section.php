<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['section:read']],
    denormalizationContext: ['groups' => ['section:write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['section:read']]),
        new GetCollection(normalizationContext: ['groups' => ['section:read']])
    ],
    order: ['id' => 'ASC'],
    paginationEnabled: false
)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['section:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['section:read', 'section:write'])]
    private ?int $range_position = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['section:read', 'section:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Projets::class, inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['section:read', 'section:write'])]
    private ?Projets $projets = null;

    #[ORM\OneToOne(inversedBy: 'section', cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true)] // nullable permet de ne pas exiger de lien obligatoire
    #[Groups(['section:read', 'section:write'])]
    private ?Photo $photo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRangePosition(): ?int
    {
        return $this->range_position;
    }

    public function setRangePosition(int $range_position): static
    {
        $this->range_position = $range_position;

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

    public function getProjets(): ?Projets
    {
        return $this->projets;
    }

    public function setProjets(?Projets $projets): static
    {
        $this->projets = $projets;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
