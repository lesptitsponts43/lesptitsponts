<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "slider")]
class Slider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $texte;

    #[ORM\Column(type: "string", length: 255)]
    private string $lien;

    #[ORM\Column(type: "string", length: 255)]
    private string $titreLien;

    #[ORM\Column(type: "string", length: 255)]
    private string $image;

    // === Getters et Setters ===

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getTexte(): string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;
        return $this;
    }

    public function getLien(): string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;
        return $this;
    }

    public function getTitreLien(): string
    {
        return $this->titreLien;
    }

    public function setTitreLien(string $titreLien): self
    {
        $this->titreLien = $titreLien;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }
}
