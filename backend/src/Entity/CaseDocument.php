<?php
// src/Entity/ClientCaseDocument.php
namespace App\Entity;

use App\Repository\CaseDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaseDocumentRepository::class)]
class CaseDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ClientCase::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientCase $case = null;

    #[ORM\Column(name: 'filename', length: 255)]
    private ?string $fileName = null;


    #[ORM\Column(name: 'file_path', length: 255)]
    private ?string $fileUrl = null;


    #[ORM\Column(length: 50)]
    private ?string $fileType = null; // "pdf" or "image"

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCase(): ?ClientCase
    {
        return $this->case;
    }
    public function setCase(ClientCase $case): static
    {
        $this->case = $case;
        return $this;
    }
    public function getFileName(): ?string
    {
        return $this->fileName;
    }
    public function setFileName(string $name): static
    {
        $this->fileName = $name;
        return $this;
    }
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }
    public function setFileUrl(string $url): static
    {
        $this->fileUrl = $url;
        return $this;
    }
    public function getFileType(): ?string
    {
        return $this->fileType;
    }
    public function setFileType(string $type): static
    {
        $this->fileType = $type;
        return $this;
    }
}
