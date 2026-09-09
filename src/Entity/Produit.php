<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[Vich\Uploadable]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: "produit_image", fileNameProperty: "image")]
    private ?File $imageFile = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Actualite::class, mappedBy: "produits")]
    private Collection $evenements; 

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

    // --- Getters & Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection|Actualite[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Actualite $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
        }

        return $this;
    }

    public function removeEvenement(Actualite $evenement): self
    {
        $this->evenements->removeElement($evenement);

        return $this;
    }
}
