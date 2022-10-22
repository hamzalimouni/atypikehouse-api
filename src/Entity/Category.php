<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        operations: [
            new Post(
                security: "is_granted('ROLE_ADMIN')",
                denormalizationContext: ['groups' => ['write:category']],
            ),
            new Get(
                normalizationContext: ['groups' => ['read:category', 'read:property']],
            ),
            new GetCollection(
                normalizationContext: ['groups' => ['read:categorycollection', 'read:property']],
            ),
            new Patch(
                security: "is_granted('ROLE_ADMIN') or object == user",
                denormalizationContext: ['groups' => ['write:category']],
            ),
            new Delete(
                security: "is_granted('ROLE_ADMIN') or object == user"
            )
        ]
    )
]
/*#[Post(
    normalizationContext: ['groups' => ['create:category']],
)]*/
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:category', 'read:category', 'read:categorycollection'])]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Groups(['read:category', 'write:category', 'read:category', 'create:category', 'read:categorycollection'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read:category', 'write:category', 'create:category', 'read:categorycollection'])]
    #[Assert\NotBlank]
    private ?bool $status = null;

    #[ORM\Column]
    #[Groups(['read:category'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: House::class)]
    #[Groups(['read:category'])]
    #[Assert\NotNull]
    private Collection $houses;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Propriety::class)]
    #[Groups(['read:category'])]
    #[Assert\NotNull]
    private Collection $proprieties;

    public function __construct()
    {
        $this->houses = new ArrayCollection();
        $this->proprieties = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();   
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
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
            $house->setCategory($this);
        }

        return $this;
    }

    public function removeHouse(House $house): self
    {
        if ($this->houses->removeElement($house)) {
            // set the owning side to null (unless already changed)
            if ($house->getCategory() === $this) {
                $house->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Propriety>
     */
    public function getProprieties(): Collection
    {
        return $this->proprieties;
    }

    public function addPropriety(Propriety $propriety): self
    {
        if (!$this->proprieties->contains($propriety)) {
            $this->proprieties->add($propriety);
            $propriety->setCategory($this);
        }

        return $this;
    }

    public function removePropriety(Propriety $propriety): self
    {
        if ($this->proprieties->removeElement($propriety)) {
            // set the owning side to null (unless already changed)
            if ($propriety->getCategory() === $this) {
                $propriety->setCategory(null);
            }
        }

        return $this;
    }
}
