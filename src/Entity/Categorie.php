<!-- Categorie.php
Fait Par Anthony Grenier le 22 févrié 2025 -->

<?php

namespace App\Entity;

use App\Entity\CategorieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\CategorieRepository::class)]


#[ORM\Table(name:'categorie')]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idCategorie')]
    private ?int $idCategorie = null;
    
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\OneToMany(targetEntity:Produit::class, mappedBy:"IdCategorie", fetch:"LAZY")]
    public function idCategorie(): ?int
    {
        return $this->idCategorie;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getProduits() : Collection {
        return $this->produits;
    }
}
