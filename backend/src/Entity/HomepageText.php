<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class HomepageText
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private string $section;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $fontWeight = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fontCategory = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function getId(): ?int { return $this->id; }

    public function getSection(): string { return $this->section; }
    public function setSection(string $section): static { $this->section = $section; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): static { $this->content = $content; return $this; }

    public function getFontWeight(): ?string { return $this->fontWeight; }
    public function setFontWeight(?string $fontWeight): static { $this->fontWeight = $fontWeight; return $this; }

    public function getFontCategory(): ?string { return $this->fontCategory; }
    public function setFontCategory(?string $fontCategory): static { $this->fontCategory = $fontCategory; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
}
