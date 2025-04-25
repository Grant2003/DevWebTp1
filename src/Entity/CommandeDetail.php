<?php

namespace App\Entity;

//-----------------------------------
//   Fichier : CommandeDetail.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
//-----------------------------------

use App\Entity\CommandeDetailRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\CommandeDetailRepository::class)]


#[ORM\Table(name:'commandeDetail')]
class CommandeDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idCommandeDetail')]
    private ?int $idCommandeDetail = null;
    
    #[ORM\Column]
    private ?int $quantite = null;
    #[ORM\Column]
    private ?int $quantiteRupture = null;

    #[ORM\ManyToOne(inversedBy: 'commandeDetails')]
    #[ORM\JoinColumn(name: 'idCommande', referencedColumnName: 'idCommande', nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'commandeDetails')]
    #[ORM\JoinColumn(name: 'produit_id', referencedColumnName: 'id', nullable: false)]
    private ?Produit $produit = null;
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function idCommandeDetail(): ?int
    {
        return $this->idCommandeDetail;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getNom(): ?string
    {
        return $this->nom;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getProduit() : Produit {
        return $this->produit;
    }
    public function getQuantite() : int {
        return $this->quantite;
    }
    public function getQuantiteRupture() : int {
        return $this->quantiteRupture;
    }
    public function setProduit(Produit $prod): static
    {
        $this->produit = $prod;

        return $this;
    }
    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }
    public function setQuantiteRupture(int $quantite): static
    {
        $this->quantiteRupture = $quantite;

        return $this;
    }
    public function setCommande(Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }
}
