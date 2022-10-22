<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use App\Repository\ProprietyValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        new Post(
            security: "is_granted('ROLE_USER')",
        ),
        new Get(),
        new GetCollection(),
        new Patch(
            security: "is_granted('ROLE_USER')",
        ),
        new Delete(
            security: "is_granted('ROLE_USER')",
        ),
        normalizationContext: ['groups' => ['read:propertyvalue']],
        denormalizationContext: ['groups' => ['write:propertyvalue']],
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'house.id' => SearchFilter::STRATEGY_EXACT,
        ]
    )
]
#[ORM\Entity(repositoryClass: ProprietyValueRepository::class)]
class ProprietyValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:propertyvalue', 'read:house'])]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:propertyvalue', 'write:propertyvalue', 'read:house'])]
    #[Assert\NotBlank]
    private ?string $value = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:propertyvalue', 'write:propertyvalue', 'read:house'])]
    #[Assert\NotNull]
    private ?Propriety $propriety = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:propertyvalue', 'write:propertyvalue'])]
    #[Assert\NotNull]
    private ?House $house = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPropriety(): ?Propriety
    {
        return $this->propriety;
    }

    public function setPropriety(?Propriety $propriety): self
    {
        $this->propriety = $propriety;

        return $this;
    }

    public function getHouse(): ?House
    {
        return $this->house;
    }

    public function setHouse(?House $house): self
    {
        $this->house = $house;

        return $this;
    }
}
