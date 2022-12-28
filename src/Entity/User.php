<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\MeController;
use App\Controller\RegisterController;
use App\Controller\UserUpdateController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN') or object == user",
        ),
        //new Post(),
        new Patch(
            security: "is_granted('ROLE_USER') and object == user || is_granted('ROLE_ADMIN')",
            name: 'update',
            uriTemplate: '/users/{id}/update',
            controller: UserUpdateController::class
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['read:user:collection', 'read:address']],
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Post(
            name: 'register',
            uriTemplate: '/register',
            controller: RegisterController::class
        ),
        //new Post(name: 'login', routeName: 'api_login_check', denormalizationContext: ['groups' => ['user:login']]),
        new GetCollection(
            normalizationContext: ['groups' => ['read:current:user']],
            name: 'me',
            uriTemplate: '/me',
            controller: MeController::class,
        ),
    ],
    normalizationContext: ['groups' => ['read:user', 'read:address', 'read:review', 'read:reservation']],
    denormalizationContext: ['groups' => ['write:user', 'write:address', 'write:review', 'write:message']],
)]
//#[Get(normalizationContext: ['groups' => ['read:user']])]

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:user', 'read:current:user', 'read:reservation', 'read:message', 'read:review', 'read:user:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['read:user', 'read:current:user', 'write:user', 'read:reservation', 'read:message', 'read:review', 'read:user:collection', 'user:login'])]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['read:user', 'read:current:user', 'read:user:collection'])]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['write:user', 'read:current:user', 'user:login'])]
    # @var string The hashed password
    #[Assert\Length(
        min: 8,
    )]
    private ?string $password = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:user', 'write:user', 'read:reservation', 'read:message', 'read:review', 'read:user:collection'])]
    #[Assert\NotBlank]
    private ?string $firstname = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:user', 'write:user', 'read:reservation', 'read:message', 'read:review', 'read:user:collection'])]
    #[Assert\NotBlank]
    private ?string $lastname = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:user', 'write:user', 'read:reservation', 'read:user:collection'])]
    #[Assert\NotBlank]
    private ?string $number = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:user', 'write:user', 'read:user:collection', 'read:reservation'])]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['read:user', 'read:user:collection'])]
    private ?string $status = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read:user', 'write:user', 'read:reservation',  'read:review', 'read:user:collection'])]
    private ?Address $address = null;

    // #[Groups(['read:user'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    // #[Groups(['read:user', 'read:user:collection'])]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    //#[Groups(['read:user'])]
    private Collection $sentmessages;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class)]
    //#[Groups(['read:user'])]
    private Collection $receivedmessages;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: House::class)]
    // #[Groups(['read:user'])]
    private Collection $houses;

    #[ORM\Column]
    #[Groups(['read:user'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Notification::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->sentmessages = new ArrayCollection();
        $this->receivedmessages = new ArrayCollection();
        $this->houses = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function isStatus(): ?string
    {
        $status = $this->status;
        $status = 'NOT_VERIFIED';
        return $status;
    }

    public function setStatus(string $status): self
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

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentmessages(): Collection
    {
        return $this->sentmessages;
    }

    public function addSentmessage(Message $sentmessage): self
    {
        if (!$this->sentmessages->contains($sentmessage)) {
            $this->sentmessages->add($sentmessage);
            $sentmessage->setSender($this);
        }

        return $this;
    }

    public function removeSentmessage(Message $sentmessage): self
    {
        if ($this->sentmessages->removeElement($sentmessage)) {
            // set the owning side to null (unless already changed)
            if ($sentmessage->getSender() === $this) {
                $sentmessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedmessages(): Collection
    {
        return $this->receivedmessages;
    }

    public function addReceivedmessage(Message $receivedmessage): self
    {
        if (!$this->receivedmessages->contains($receivedmessage)) {
            $this->receivedmessages->add($receivedmessage);
            $receivedmessage->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedmessage(Message $receivedmessage): self
    {
        if ($this->receivedmessages->removeElement($receivedmessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedmessage->getReceiver() === $this) {
                $receivedmessage->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, House>
     */
    public function getHouses(): Collection
    {
        return $this->houses;
    }

    public function addHouse(House $house): self
    {
        if (!$this->houses->contains($house)) {
            $this->houses->add($house);
            $house->setOwner($this);
        }

        return $this;
    }

    public function removeHouse(House $house): self
    {
        if ($this->houses->removeElement($house)) {
            // set the owning side to null (unless already changed)
            if ($house->getOwner() === $this) {
                $house->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUserId($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUserId() === $this) {
                $notification->setUserId(null);
            }
        }

        return $this;
    }
}
