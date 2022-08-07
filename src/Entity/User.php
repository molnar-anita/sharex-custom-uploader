<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at;

    #[ORM\OneToMany(mappedBy: 'files', targetEntity: File::class, orphanRemoval: true)]
    private Collection $files;

    public function __construct() {
        $this->created_at = new DateTimeImmutable();
        $this->files = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection {
        return $this->files;
    }

    public function addFile(File $file): self {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setUser($this);
        }

        return $this;
    }

    public function removeFile(File $file): self {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getUser() === $this) {
                $file->setUser(null);
            }
        }

        return $this;
    }

    public function equals(User $user): bool {
        return
            $this->getUuid() === $user->getUuid() &&
            $this->getId() === $user->getId() &&
            $this->getApiKey() === $user->getApiKey();
    }

    public function getUuid(): ?string {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self {
        $this->uuid = $uuid;

        return $this;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getApiKey(): ?string {
        return $this->uuid;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): User {
        $this->created_at = $created_at;

        return $this;
    }
}
