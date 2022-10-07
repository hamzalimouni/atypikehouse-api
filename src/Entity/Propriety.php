<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProprietyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        normalizationContext: ['groups' => ['read:property']],
        denormalizationContext: ['groups' => ['write:property']],
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'category.name' => SearchFilter::STRATEGY_EXACT,
        ]
    )
]
#[ORM\Entity(repositoryClass: ProprietyRepository::class)]
class Propriety
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:property', 'read:propertyvalue', 'read:house'])]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:property', 'write:property', 'read:propertyvalue', 'read:house'])]
    private ?string $name = null;

    #[ORM\Column(length: 128)]
    #[Groups(['read:property', 'write:property', 'read:propertyvalue'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['read:property', 'write:property', 'read:propertyvalue'])]
    private ?bool $isRequired = null;

    #[ORM\Column]
    #[Groups(['read:property', 'write:property', 'read:propertyvalue'])]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'proprieties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:property', 'write:property', 'read:propertyvalue'])]
    private ?Category $category = null;

    #[ORM\Column]
    #[Groups(['read:propertyvalue', 'read:propertyvalue'])]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isIsRequired(): ?bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): self
    {
        $this->isRequired = $isRequired;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
