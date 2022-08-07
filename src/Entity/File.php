<?php

namespace App\Entity;

use App\Repository\FileRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\ByteString;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $mime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expire_in = null;

    #[ORM\Column]
    private ?bool $access_once = null;

    #[ORM\Column(length: 255)]
    private ?string $deleteToken = null;

    #[ORM\ManyToOne(inversedBy: 'user_id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct() {
        $this->created_at = new DateTimeImmutable();
        $this->deleteToken = ByteString::fromRandom(16)->toString();
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): File {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDeleteToken(): ?string {
        return $this->deleteToken;
    }

    public function setDeleteToken(string $deleteToken): File {
        $this->deleteToken = $deleteToken;

        return $this;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string {
        return $this->path;
    }

    public function setPath(string $path): self {
        $this->path = $path;

        return $this;
    }

    public function getUuid(): ?string {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self {
        $this->uuid = $uuid;

        return $this;
    }

    public function getMime(): ?string {
        return $this->mime;
    }

    public function setMime(string $mime): self {
        $this->mime = $mime;

        return $this;
    }

    public function getExpireIn(): ?\DateTimeInterface {
        return $this->expire_in;
    }

    public function setExpireIn(?\DateTimeInterface $expire_in): self {
        $this->expire_in = $expire_in;

        return $this;
    }

    public function isAccessOnce(): ?bool {
        return $this->access_once;
    }

    public function setAccessOnce(bool $access_once): self {
        $this->access_once = $access_once;

        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;

        return $this;
    }
}
