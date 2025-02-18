<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['projet:read']],
    denormalizationContext: ['groups' => ['projet:write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['projet:read']]),
        new GetCollection(normalizationContext: ['groups' => ['projet:read']]),
        new Put(normalizationContext: ['groups' => ['projet:write']]),
        new Delete(normalizationContext: ['groups' => ['projet:read']]),
        new Post(normalizationContext: ['groups' => ['projet:write']])
    ],
    order: ['id' => 'ASC'],
    paginationEnabled: false
)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['projet:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['projet:read', 'projet:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Groups(['projet:read', 'projet:write'])]
    private ?string $color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
