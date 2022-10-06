<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['read:message', 'read:user:profile']],
    denormalizationContext: ['groups' => ['write:message']],
)]

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read:message')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('read:message', 'write:message')]
    private ?string $content = null;

    #[ORM\Column(length: 128)]
    #[Groups('read:message', 'write:message')]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'sentmessages')]
    #[Groups('read:message', 'write:message', 'read:user', 'write:user')]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'receivedmessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('read:message', 'write:message', 'read:user', 'write:user')]
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
