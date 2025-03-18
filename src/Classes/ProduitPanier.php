<?php

namespace App\Classes;
//-----------------------------------
//   Fichier : ProduitPanier.php
//   Par:      Anthony Grenier
//   Date :    2025-3-16
//-----------------------------------
Class ProduitPanier
{
    public ?int $id = null;
    public ?string $nom = null;
    public ?int $prix = null;
    public ?int $quantiteCommande = 1;
}