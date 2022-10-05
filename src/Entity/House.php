<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\HouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['read:house', 'read:address', 'read:review']],
    denormalizationContext: ['groups' => ['write:house']],
)]
#[ORM\Entity(repositoryClass: HouseRepository::class)]
class House
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:house', 'read:reservation', 'read:user'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user'])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'houses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user'])]
    private ?Category $category = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user'])]
    private ?float $price = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:house', 'write:house'])]
    private ?int $nbPerson = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house'])]
    private ?float $surface = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house'])]
    private ?bool $disponible = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user'])]
    private ?string $photos = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house'])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(['read:house', 'read:reservation'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'house', targetEntity: EquipementValue::class)]
    #[Groups(['read:house', 'write:house'])]
    private Collection $equipements;

    #[ORM\OneToMany(mappedBy: 'house', targetEntity: ProprietyValue::class)]
    #[Groups(['read:house', 'write:house'])]
    private Collection $properties;

    #[ORM\OneToMany(mappedBy: 'house', targetEntity: Reservation::class)]
    #[Groups(['read:house', 'write:house'])]
    private Collection $reservations;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:house', 'write:house'])]
    private ?Address $address = null;

    #[ORM\OneToMany(mappedBy: 'house', targetEntity: Review::class)]
    #[Groups(['read:house', 'write:house'])]
    private Collection $reviews;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getNbPerson(): ?int
    {
        return $this->nbPerson;
    }

    public function setNbPerson(int $nbPerson): self
    {
        $this->nbPerson = $nbPerson;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(float $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getPhotos(): ?string
    {
        return $this->photos;
    }

    public function setPhotos(string $photos): self
    {
        $this->photos = $photos;

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

    /**
     * @return Collection<int, EquipementValue>
     */
    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(EquipementValue $equipement): self
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
            $equipement->setHouse($this);
        }

        return $this;
    }

    public function removeEquipement(EquipementValue $equipement): self
    {
        if ($this->equipements->removeElement($equipement)) {
            // set the owning side to null (unless already changed)
            if ($equipement->getHouse() === $this) {
                $equipement->setHouse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProprietyValue>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(ProprietyValue $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
            $property->setHouse($this);
        }

        return $this;
    }

    public function removeProperty(ProprietyValue $property): self
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getHouse() === $this) {
                $property->setHouse(null);
            }
        }

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
            $reservation->setHouse($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getHouse() === $this) {
                $reservation->setHouse(null);
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
            $review->setHouse($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getHouse() === $this) {
                $review->setHouse(null);
            }
        }

        return $this;
    }
}
