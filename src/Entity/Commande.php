<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $date = null;

    #[ORM\Column]
    private ?float $montant_total = null;

    // --- AJOUT DES PROPRIÉTÉS MANQUANTES ---
    #[ORM\Column(type: 'float', options: ['default' => 0])]
    private ?float $frais_port = 0.0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transporteur_nom = null;
    // ---------------------------------------

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    /**
     * @var Collection<int, LigneDeCommande>
     */
    #[ORM\OneToMany(targetEntity: LigneDeCommande::class, mappedBy: 'commande', orphanRemoval: true)]
    private Collection $ligneDeCommandes;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AdresseDeLivraison $adressedeLivraison = null;

    public function __construct()
    {
        $this->ligneDeCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montant_total;
    }

    public function setMontantTotal(float $montant_total): static
    {
        $this->montant_total = $montant_total;

        return $this;
    }

    // --- AJOUT DES GETTERS & SETTERS MANQUANTS ---
    public function getFraisPort(): ?float
    {
        return $this->frais_port;
    }

    public function setFraisPort(float $frais_port): static
    {
        $this->frais_port = $frais_port;

        return $this;
    }

    public function getTransporteurNom(): ?string
    {
        return $this->transporteur_nom;
    }

    public function setTransporteurNom(?string $transporteur_nom): static
    {
        $this->transporteur_nom = $transporteur_nom;

        return $this;
    }
    // ----------------------------------------------

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection<int, LigneDeCommande>
     */
    public function getLigneDeCommandes(): Collection
    {
        return $this->ligneDeCommandes;
    }

    public function addLigneDeCommande(LigneDeCommande $ligneDeCommande): static
    {
        if (!$this->ligneDeCommandes->contains($ligneDeCommande)) {
            $this->ligneDeCommandes->add($ligneDeCommande);
            $ligneDeCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLigneDeCommande(LigneDeCommande $ligneDeCommande): static
    {
        if ($this->ligneDeCommandes->removeElement($ligneDeCommande)) {
            if ($ligneDeCommande->getCommande() === $this) {
                $ligneDeCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getAdressedeLivraison(): ?AdresseDeLivraison
    {
        return $this->adressedeLivraison;
    }

    public function setAdressedeLivraison(?AdresseDeLivraison $adressedeLivraison): static
    {
        $this->adressedeLivraison = $adressedeLivraison;

        return $this;
    }
}