<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    GetCollection(
        //security: "is_granted('ROLE_ADMIN')",
        normalizationContext: ['groups' => ['read:equipmentcollection']],
    ),
    Post(
        security: "is_granted('ROLE_ADMIN')",
        denormalizationContext: ['groups' => ['write:equipment']],
    ),
    Patch(
        security: "is_granted('ROLE_ADMIN')",
        denormalizationContext: ['groups' => ['write:equipment']],
    ),
    Delete(
        security: "is_granted('ROLE_ADMIN')",
    )
]
#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:equipment', 'read:house', 'read:equipmentcollection'])]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:equipment', 'write:equipment', 'read:house', 'read:equipmentcollection'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read:equipment', 'write:equipment', 'read:equipmentcollection'])]
    private ?bool $status = null;

    #[ORM\Column]
    #[Groups(['read:equipment'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();   
        $this->status = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
