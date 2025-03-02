<!-- Categorie.php
Fait Par Anthony Grenier le 22 févrié 2025 -->
<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]

class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 150)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $qtte_stock = null;
    

    #[ORM\Column]
    private ?int $Qtte_seuil_min = null;

    #[ORM\ManyToOne(targetEntity:Categorie::class, inversedBy:"produits", cascade:["persist"])]
    #[ORM\JoinColumn(name:'idCategorie', referencedColumnName:'idCategorie')]
    private $idCategorie;

    public function __construct()
    {
        $this->id_categorie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQtte_Stock(): ?int
    {
        return $this->qtte_stock;
    }
    

    public function setQtteStock(int $qtte_stock): static
    {
        $this->qtte_stock = $qtte_stock;

        return $this;
    }

    public function getQtteSeuilMin(): ?int
    {
        return $this->Qtte_seuil_min;
    }

    public function setQtteSeuilMin(int $Qtte_seuil_min): static
    {
        $this->Qtte_seuil_min = $Qtte_seuil_min;

        return $this;
    }

    public function getIdCategorie(): ?Categorie
    {
        return $this->idCategorie;
    }
}
