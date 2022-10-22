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
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        new Post(
            security: "is_granted('ROLE_USER')",
        ),
        new Get(
            security: "object.sender == user or object.receiver == user",
        ),
        new GetCollection(
            security: "object.sender == user or object.receiver == user",
        ),
        new Patch(
            security: "object.sender == user or object.receiver == user",
        ),
        new Delete(
            //security: "object.sender == user or object.receiver == user", 
        ),
        normalizationContext: ['groups' => ['read:message']],
        denormalizationContext: ['groups' => ['write:message']],
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'sender.id' => SearchFilter::STRATEGY_EXACT,
            'receiver.id' => SearchFilter::STRATEGY_EXACT,
        ]
    )
]

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read:message')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:message', 'write:message'])]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\Column(length: 128)]
    #[Groups(['read:message', 'write:message'])]
    #[Assert\NotNull]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'sentmessages')]
    #[Groups(['read:message', 'write:message', 'read:user', 'write:user'])]
    #[Assert\NotNull]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'receivedmessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:message', 'write:message', 'read:user', 'write:user'])]
    #[Assert\NotNull]
    private ?User $receiver = null;

    #[ORM\Column]
    #[Groups('read:message')]
    private ?\DateTimeImmutable $createdAt = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}
