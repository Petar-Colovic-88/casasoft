<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Traits\Timestampable;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['global:read', 'user:read']],
    denormalizationContext: ['groups' => ['global:write', 'user:write']]
)]
#[GetCollection(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')")]
#[Get(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')")]
#[Post(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')")]
#[Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')")]
#[Patch(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['property:read', 'user:read', 'user:write'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['property:read', 'user:read', 'user:write'])]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Groups(['user:write'])]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Groups(['property:read', 'user:read', 'user:write'])]
    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Property::class, orphanRemoval: true)]
    private Collection $properties;

    #[ORM\ManyToMany(targetEntity: Property::class, mappedBy: "permissions")]
    #[ORM\JoinTable(name: "property_permission")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "property_id", referencedColumnName: "id")]
    private Collection $permissions;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Property>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Property $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
            $property->setUser($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->removeElement($property)) {
            if ($property->getUser() === $this) {
                $property->setUser(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function eraseCredentials()
    {
//        $this->password = null;
    }

    /**
     * @return Collection<int, Property>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Property $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    public function removePermission(Property $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }

    public function hasPermission(Property $property): bool
    {
        return !$this->permissions->filter(fn ($model) => $model->getId() === $property->getId())->isEmpty();
    }
}
