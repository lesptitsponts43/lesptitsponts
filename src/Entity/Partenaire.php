<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Vich\Uploadable]
class Partenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    private string $titre;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: "partner_image", fileNameProperty: "image")]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le lien est obligatoire.")]
    private string $lien;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTime('now');
    }

    // ----- Getters & Setters -----

    public function getId(): ?int { return $this->id; }

    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getLien(): string { return $this->lien; }
    public function setLien(string $lien): self { $this->lien = $lien; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }

    public function getImageFile(): ?File { return $this->imageFile; }
    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
}
