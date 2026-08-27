<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $illustration = null;

    /**
     * @var Collection<int, Categories>
     */
  
    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy: 'products')]
    private Collection $categories;

  
    // LigneDeCommande qui portera le lien ManyToOne vers Product.
    //relation unidirectionnelle L'entité Product n'a pas besoin de connaître la liste des lignes de commande associées
    //Contrainte non-nullable pour le OneToOne strict avec le Stock
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stock $stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contenance = null;

    // (constructeur) __construct() : Instancie $categories en tant qu'ArrayCollection 
    //pour éviter toute erreur PHP null lors de l'ajout de catégories.
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(string $illustration): static
    {
        $this->illustration = $illustration;

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }
    // addCategory() / removeCategory() : 
    //Méthodes sécurisées pour ajouter ou retirer une catégorie de la collection 
    //sans risquer de créer des doublons.
    public function addCategory(Categories $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Categories $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getContenance(): ?string
    {
        return $this->contenance;
    }

    public function setContenance(?string $contenance): static
    {
        $this->contenance = $contenance;

        return $this;
    }
}