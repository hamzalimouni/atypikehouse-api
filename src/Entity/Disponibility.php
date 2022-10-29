<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Repository\DisponibilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DisponibilityRepository::class)]
#[
    ApiResource(
        operations: [
            new Post(),
            new GetCollection(),
            new Get(),
        ]
    )
]
class Disponibility
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?House $House = null;

    #[Groups(['read:house', 'write:house', 'read:housecollcetion'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHouse(): ?House
    {
        return $this->House;
    }

    public function setHouse(?House $House): self
    {
        $this->House = $House;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
