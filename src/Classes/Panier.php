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
        //on recherche un produit avec l'id correspondant et le supprime
        foreach ($this->panier as $key => $produit) {
            if ($produit->id === $id) {
                unset($this->panier[$key]); 
                $this->panier = array_values($this->panier);  //on re-index le panier
                return; 
            }
        }
    }
    
    public function ajouterProduit(ProduitPanier $produit): void {
        $this->panier[] = $produit;
    }

    public function calculerSommePrix(): float {
        return array_reduce($this->panier, function ($temp, $produit) {
            return $temp + $produit->prix*$produit->quantiteCommande; 
        }, 0);
    }

    public function compterProduitsNomsDifferents(): int {
        $noms = array_map(function ($produit) {
            return $produit->nom; 
        }, $this->panier);

        return count(array_unique($noms));
    }

    public function compterProduitsTotal(): int {
        return array_reduce($this->panier, function ($total, $produit) {
            return $total + $produit->quantiteCommande; 
        }, 0);
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
        //trouve le produit et incrémente sa quantité
        foreach ($this->panier as $produit) {
            if ($produit->id === $id) {
                if($produit->quantiteCommande >= 20){
                    return;
                }
                $produit->quantiteCommande += 1;  
                return; 
            }
        }
    }
    public function majQuantiteProduit(int $id, int $quantite): void {
        //trouve le produit et change sa quantité

        foreach ($this->panier as $produit) {
            if ($produit->id === $id) {
                $produit->quantiteCommande =$quantite; 
                //si la quantite depasse 20 on la met a 20 et retourne  
                if($produit->quantiteCommande >= 20){
                    $produit->quantiteCommande = 20;  

                    return;
                }
                return;  
            }
        }
    }
    public function viderPanier(): void {
        $this->panier = [];
    }
}
