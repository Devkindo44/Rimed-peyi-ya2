<?php

namespace App\Entity;

use App\Repository\AdresseDeLivraisonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdresseDeLivraisonRepository::class)]
class AdresseDeLivraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse_postale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code_postale = null;

    #[ORM\ManyToOne(inversedBy: 'adresseDeLivraisons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    // 🌟 NOUVEAUTÉ : Propriété téléphone
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdressePostale(): ?string
    {
        return $this->adresse_postale;
    }

    public function setAdressePostale(?string $adresse_postale): static
    {
        $this->adresse_postale = $adresse_postale;

        return $this;
    }

    public function getCodePostale(): ?string
    {
        return $this->code_postale;
    }

    public function setCodePostale(?string $code_postale): static
    {
        $this->code_postale = $code_postale;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    // 🌟 NOUVEAUTÉ : Getter & Setter téléphone
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }
}