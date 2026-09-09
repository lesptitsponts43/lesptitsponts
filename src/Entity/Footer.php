<?php

namespace App\Entity;

use App\Repository\FooterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FooterRepository::class)]
class Footer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $titre;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $lien;

    public function getId(): ?int
    {
        return $id;
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

    public function getLien(): string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;
        return $this;
    }
}
