<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\NotificationController;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[
    ApiResource(
        operations: [
            new GetCollection(
                controller: NotificationController::class,
                security: "is_granted('ROLE_USER')",
            ),
        ],
        normalizationContext: ['groups' => ['read:message']],
        denormalizationContext: ['groups' => ['write:message']],
    ),
]

class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?int $data = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): self
    {
        $this->user = $user;

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

    public function getData(): ?int
    {
        return $this->data;
    }

    public function setData(int $data): self
    {
        $this->data = $data;

        return $this;
    }
}
