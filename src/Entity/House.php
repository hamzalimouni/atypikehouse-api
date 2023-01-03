<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\HouseController;
use App\Repository\HouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        operations: [
            new GetCollection(
                normalizationContext: ['groups' => ['read:housecollcetion']]
            ),
            new Get(
                // security: "object.status == 'APPROVED'",
                controller: HouseController::class,
                normalizationContext: ['groups' => ['read:house', 'read:address', 'read:review', 'read:reservation']],
            ),
            new Post(
                controller: HouseController::class,
                deserialize: false,
                security: "is_granted('ROLE_USER')",
                denormalizationContext: ['groups' => ['write:house', 'write:address', 'write:review']],
                //inputFormats: ['multipart' => ['multipart/form-data']]
            ),
            new Patch(
                controller: HouseController::class,
                security: "is_granted('ROLE_ADMIN')",
                denormalizationContext: ['groups' => ['write:house', 'write:address', 'write:review']],
            ),
            new Delete(
                security: "is_granted('ROLE_ADMIN') or object.owner == user",
            )
        ]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'title' => SearchFilter::STRATEGY_PARTIAL,
            'owner.id' => SearchFilter::STRATEGY_EXACT,
            'address.city' => SearchFilter::STRATEGY_EXACT,
            'address.country' => SearchFilter::STRATEGY_EXACT,
            'status' => SearchFilter::STRATEGY_EXACT,
            'category.id' => SearchFilter::STRATEGY_EXACT,
        ]
    ),
    ApiFilter(
        RangeFilter::class,
        properties: ['rooms', 'nbPerson', 'price']
    ),
    ApiFilter(
        OrderFilter::class,
        properties: ['createdAt', 'price']
    ),
    ApiFilter(
        DateFilter::class,
        properties: ['reservations.fromDate', 'reservations.toDate', 'disponibilities.date']
    ),
]

#[ORM\Entity(repositoryClass: HouseRepository::class)]
class House
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:house', 'read:reservation', 'read:user', 'read:category', 'read:housecollcetion'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user', 'read:category', 'read:housecollcetion'])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'houses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user', 'read:housecollcetion'])]
    #[Assert\NotNull]
    private ?Category $category = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user', 'read:category', 'read:housecollcetion'])]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house', 'read:reservation', 'read:user', 'read:category', 'read:housecollcetion'])]
    #[Assert\GreaterThan(value: 0)]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion', 'read:reservation'])]
    #[Assert\GreaterThan(value: 0)]
    private ?int $nbPerson = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion'])]
    #[Assert\GreaterThan(value: 0)]
    private ?float $surface = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion', 'read:reservation'])]
    #[Assert\GreaterThan(value: 0)]
    private ?int $rooms = null;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion', 'read:reservation'])]
    #[Assert\GreaterThan(value: 0)]
    private ?int $beds = null;

    #[ORM\OneToMany(mappedBy: 'House', targetEntity: Image::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion', 'read:reservation'])]
    #[Assert\NotNull]
    private Collection $images;

    #[ORM\Column]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion'])]
    #[Assert\NotBlank]
    public ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'House', targetEntity: Disponibility::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['read:house', 'write:house'])]
    private Collection $disponibilities;

    #[ORM\OneToMany(mappedBy: 'house', targetEntity: ProprietyValue::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['read:house', 'write:house'])]
    #[Assert\NotNull]
    private Collection $properties;

    #[ORM\OneToMany(mappedBy: 'house', targetEntity: Reservation::class)]
    #[Groups(['read:house'])]
    private Collection $reservations;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion', 'read:reservation'])]
    #[Assert\NotNull]
    private ?Address $address = null;

    #[ORM\OneToMany(mappedBy: 'house', targetEntity: Review::class, cascade: ['persist', 'remove'])]
    #[Groups(['read:house', 'read:housecollcetion'])]
    #[Assert\NotNull]
    private Collection $reviews;

    #[ORM\ManyToMany(targetEntity: Equipement::class)]
    #[Groups(['read:house', 'write:house'])]
    #[Assert\NotNull]
    private Collection $equipments;

    #[ORM\ManyToOne(inversedBy: 'houses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:house', 'write:house', 'read:housecollcetion'])]
    public ?User $owner = null;

    #[ORM\Column]
    #[Groups(['read:house', 'read:reservation'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->equipments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
        $this->disponibilities = new ArrayCollection();
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

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipement $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipement $equipment): self
    {
        $this->equipments->removeElement($equipment);

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setHouse($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getHouse() === $this) {
                $image->setHouse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Disponibility>
     */
    public function getDisponibilities(): Collection
    {
        return $this->disponibilities;
    }

    public function addDisponibility(Disponibility $disponibility): self
    {
        if (!$this->disponibilities->contains($disponibility)) {
            $this->disponibilities->add($disponibility);
            $disponibility->setHouse($this);
        }

        return $this;
    }

    public function removeDisponibility(Disponibility $disponibility): self
    {
        if ($this->disponibilities->removeElement($disponibility)) {
            // set the owning side to null (unless already changed)
            if ($disponibility->getHouse() === $this) {
                $disponibility->setHouse(null);
            }
        }

        return $this;
    }

    public function getBeds(): ?int
    {
        return $this->beds;
    }

    public function setBeds(int $beds): self
    {
        $this->beds = $beds;

        return $this;
    }
}
