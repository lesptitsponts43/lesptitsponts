<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $nom = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    private ?string $prenom = null;

    #[ORM\Column(type: "string", length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: '/^\+?\d{7,20}$/',
        message: "Le numéro de téléphone n'est pas valide."
    )]
    private ?string $tel = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $mail = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le sujet est obligatoire.")]
    private ?string $sujet = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Le message est obligatoire.")]
    private ?string $message = null;

    // --------------------
    // Getters & Setters
    // --------------------
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;
        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}