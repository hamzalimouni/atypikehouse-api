<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        operations: [
            /* new Post(),
            new Get()*/],
        normalizationContext: ['groups' => ['read:address']],
        denormalizationContext: ['groups' => ['write:address']],
    ),
]
#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:address'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:address', 'write:address'])]
    #[Assert\NotBlank]
    private ?string $address = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:address', 'write:address', 'read:housecollcetion'])]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['read:address', 'write:address'])]
    #[Assert\NotBlank]
    private ?string $zipcode = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:address', 'write:address', 'read:housecollcetion'])]
    #[Assert\NotBlank]
    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:address', 'write:address', 'read:housecollcetion'])]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:address', 'write:address', 'read:housecollcetion'])]
    private ?float $latitude = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
