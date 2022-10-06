<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Traits\Timestampable;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['global:read', 'property:read']],
    denormalizationContext: ['groups' => ['global:write', 'property:write']]
)]
#[GetCollection]
#[Get(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MANAGER') and object.getUser() == user) or (is_granted('ROLE_AGENT') and object.hasPermission(user))")]
#[Post(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')")]
#[Put(securityPostDenormalize: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MANAGER') and previous_object.getUser() == user)")]
#[Patch(securityPostDenormalize: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MANAGER') and previous_object.getUser() == user)")]
#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Property
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['property:read', 'property:write'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['property:read', 'property:write'])]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[Groups(['property:read', 'property:write'])]
    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[Groups(['property:read', 'property:write'])]
    #[ORM\Column]
    private ?float $price = null;

    #[Groups(['property:read', 'property:write'])]
    #[ORM\Column]
    private ?int $numberOfRooms = null;

    #[Groups(['property:read', 'property:write'])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Groups(['property:read'])]
    #[ORM\ManyToOne(inversedBy: 'properties')]
    #[ORM\JoinColumn(name: "user_id")]
    private ?User $user = null;

    #[Groups(['property:read', 'property:write'])]
    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Description::class, cascade: ['persist'])]
    private Collection $description;

    #[Groups(['property:write'])]
    #[ApiProperty(securityPostDenormalize: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MANAGER') and object.getUser() == user)")]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "permissions")]
    #[ORM\JoinTable(name: "property_permission")]
    #[ORM\JoinColumn(name: "property_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "user_id", referencedColumnName: "id")]
    private Collection $permissions;

    public function __construct()
    {
        $this->description = new ArrayCollection();
        $this->permissions = new ArrayCollection();
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

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

    public function getNumberOfRooms(): ?int
    {
        return $this->numberOfRooms;
    }

    public function setNumberOfRooms(int $numberOfRooms): self
    {
        $this->numberOfRooms = $numberOfRooms;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Description>
     */
    public function getDescription(): Collection
    {
        return $this->description;
    }

    public function addDescription(Description $description): self
    {
        if (!$this->description->contains($description)) {
            $this->description->add($description);
            $description->setProperty($this);
        }

        return $this;
    }

    public function removeDescription(Description $description): self
    {
        if ($this->description->removeElement($description)) {
            if ($description->getProperty() === $this) {
                $description->setProperty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(User $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    public function removePermission(User $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }

    public function hasPermission(User $user): bool
    {
        return !$this->permissions->filter(fn ($model) => $model->getId() === $user->getId())->isEmpty();
    }
}
