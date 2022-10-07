<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
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
    normalizationContext: ['groups' => ['read:user', 'read:address', 'read:message', 'read:review']],
    denormalizationContext: ['groups' => ['write:user']],
)]
//#[Get(normalizationContext: ['groups' => ['read:user']])]

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read:user', 'read:reservation', 'read:message', 'read:review')]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    /**
     * @Assert\Email(
     * message = "Adresse email '{{ value }}' non valide.")
     */
    #[Groups(['read:user', 'write:user', 'read:reservation', 'read:message', 'read:review'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['read:user', 'write:user'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     * @Assert\Length(
     *      min = 8,
     *      max = 32,
     *      minMessage = "Votre mot de passe doit contenir au minimum {{ limit }} caractères",
     *      maxMessage = "Votre mot de passe doit contenir au maximum {{ limit }} caractères"
     * )
     */
    #[ORM\Column]
    #[Groups(['write:user'])]
    private ?string $password = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:user', 'write:user', 'read:reservation', 'read:message', 'read:review'])]
    /**
     * @Assert\NotBlank(message="Veuillez renseigner votre prénom")
     */
    private ?string $firstname = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:user', 'write:user', 'read:reservation', 'read:message', 'read:review'])]
    /**
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     */
    private ?string $lastname = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:user', 'write:user', 'read:reservation'])]
    /**
     * @Assert\NotBlank(message="Veuillez renseigner votre numéro de téléphone")
     */
    private ?string $number = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:user', 'write:user'])]
    /**
     * @Assert\NotBlank(message="Veuillez renseigner votre date de naissance")
     */
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:user', 'write:user'])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(['read:user'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['read:user'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:user', 'write:user', 'read:reservation'])]
    private ?Address $address = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    #[Groups(['read:user'])]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    #[Groups(['read:user'])]
    private Collection $sentmessages;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class)]
    #[Groups(['read:user'])]
    private Collection $receivedmessages;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: House::class)]
    #[Groups(['read:user'])]
    private Collection $houses;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->sentmessages = new ArrayCollection();
        $this->receivedmessages = new ArrayCollection();
        $this->houses = new ArrayCollection();
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
        return $this->status;
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
}
