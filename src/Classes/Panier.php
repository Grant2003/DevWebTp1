<?php

namespace App\Classes;

//-----------------------------------
//   Fichier : Panier.php
//   Par:      Anthony Grenier
//   Date :    2025-3-16
//-----------------------------------
class Panier {
    public array $panier;

    public function __construct() {
        $this->panier = [];
    }
    public function supprimerProduitParId(int $id): void {
        foreach ($this->panier as $key => $produit) {
            if ($produit->id === $id) {
                unset($this->panier[$key]); 
                $this->panier = array_values($this->panier); 
                return; 
            }
        }
    }
    
    public function ajouterProduit(ProduitPanier $produit): void {
        $this->panier[] = $produit;
    }

    public function calculerSommePrix(): float {
        return array_reduce($this->panier, function ($carry, $produit) {
            return $carry + $produit->prix*$produit->quantiteCommande; 
        }, 0);
    }

    public function compterProduitsNomsDifferents(): int {
        $noms = array_map(function ($produit) {
            return $produit->nom; 
        }, $this->panier);

        return count(array_unique($noms));
    }

    public function compterProduitsTotal(): int {
        return count($this->panier);
    }

    public function existenceProduitParId(int $id): ?bool {
        foreach ($this->panier as $produit) {
            if ($produit->id === $id) { 
                return true;
            }
        }

        return false; 
    }
    public function incrementerQuantiteProduit(int $id): void {
        foreach ($this->panier as $produit) {
            if ($produit->id === $id) {
                $produit->quantiteCommande += 1;  
                return; 
            }
        }
    }
    public function majQuantiteProduit(int $id, int $quantite): void {
        foreach ($this->panier as $produit) {
            if ($produit->id === $id) {
                $produit->quantiteCommande =$quantite;  
                return;  
            }
        }
    }
    public function viderPanier(): void {
        $this->panier = [];
    }
}
