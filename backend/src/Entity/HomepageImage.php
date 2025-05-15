<?php

namespace App\Entity;

use App\Repository\HomepageImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomepageImageRepository::class)]
class HomepageImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $type = null; // banner or carousel

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $imagePath = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $uploadedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeInterface $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;
        return $this;
    }
}
