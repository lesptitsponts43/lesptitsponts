<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class BlocAccueil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Le texte est obligatoire.")]
    private ?string $texte = null;

    // --------------------
    // Getters & Setters
    // --------------------
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

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;
        return $this;
    }
}