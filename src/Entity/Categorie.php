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
    private ?string $Description = null;

    #[ORM\OneToMany(targetEntity:Produit::class, mappedBy:"IdCategorie", fetch:"LAZY")]
    public function idCategorie(): ?int
    {
        return $this->idCategorie;
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

    public function getProduits() : Collection {
        return $this->produits;
    }
}
