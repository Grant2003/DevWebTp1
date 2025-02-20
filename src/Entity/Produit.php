<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Nom = null;

    #[ORM\Column(length: 150)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?float $float = null;

    #[ORM\Column]
    private ?int $Qtte_stock = null;

    #[ORM\Column]
    private ?int $Qtte_seuil_min = null;

    #[ORM\ManyToOne(targetEntity:Categorie::class, inversedBy:"produits", cascade:["persist"])]
    #[ORM\JoinColumn(name:'idCategorie', referencedColumnName:'idCategorie')]
    private $mainRole;

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
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getFloat(): ?float
    {
        return $this->float;
    }

    public function setFloat(float $float): static
    {
        $this->float = $float;

        return $this;
    }

    public function getQtteStock(): ?int
    {
        return $this->Qtte_stock;
    }

    public function setQtteStock(int $Qtte_stock): static
    {
        $this->Qtte_stock = $Qtte_stock;

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

    public function getIdCategorie(): ?int
    {
        return $this->idCategorie;
    }

    public function setIdCategorie(int $idCategorie): static
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }

    public function addIdCategorie(Categories $idCategorie): static
    {
        if (!$this->id_categorie->contains($idCategorie)) {
            $this->id_categorie->add($idCategorie);
            $idCategorie->setCategorie($this);
        }

        return $this;
    }

    public function removeIdCategorie(Categories $idCategorie): static
    {
        if ($this->id_categorie->removeElement($idCategorie)) {
            // set the owning side to null (unless already changed)
            if ($idCategorie->getCategorie() === $this) {
                $idCategorie->setCategorie(null);
            }
        }

        return $this;
    }
}
