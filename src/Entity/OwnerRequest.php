<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\OwnerRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\OwnerRequestController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OwnerRequestRepository::class)]
#[
    ApiResource(
        operations: [
            new Post(
                security: "is_granted('ROLE_USER')"
            ),
            new Get(
                security: "is_granted('ROLE_USER')",
            ),
            new GetCollection(
                security: "is_granted('ROLE_USER')",
            ),
            new Patch(
                controller: OwnerRequestController::class,
                security: "is_granted('ROLE_ADMIN')",
            ),
        ],
        normalizationContext: ['groups' => ['read:request', 'read:user']],
        denormalizationContext: ['groups' => ['write:request', 'write:user']],
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'user.id' => SearchFilter::STRATEGY_PARTIAL,
        ]
    ),
]
class OwnerRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:request'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'ownerRequest', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:request', 'write:request', 'read:user', 'write:user'])]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read:request', 'write:request'])]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:request', 'write:request'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:request', 'write:request'])]
    private ?string $procedures = null;

    #[ORM\Column]
    #[Groups(['read:request'])]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->setStatus('UNDER_REVIEW');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProcedures(): ?string
    {
        return $this->procedures;
    }

    public function setProcedures(string $procedures): self
    {
        $this->procedures = $procedures;

        return $this;
    }
}
