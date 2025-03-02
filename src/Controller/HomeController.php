<?php

namespace App\Controller;
//-----------------------------------
//    Fichier : HomeController.php
//    Par:      Anthony Grenier
//    Date :    2025-2-22
//-----------------------------------

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $em = null;

    #[Route('/', name: 'app_home')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->em = $doctrine->getManager();

        $categorie = $request->query->get('categorie'); 
        $searchField = $request->request->get('search_field'); 

        $categories = $this->retrieveAllCategories();
        
        $produits = $this->retrieveProduits($categorie, $searchField);
        


        return $this->render('home/index.html.twig', ['produits' => $produits, 'categories' => $categories]);
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/produits/{idProduit}', name:'produit_modal')]
    public function infoProduit($idProduit, Request $request, ManagerRegistry $doctrine): Response {

        $this->em = $doctrine->getManager();

        $produit = $this->em->getRepository(Produit::class)->find($idProduit);

        return $this->render('home/produit.modal.twig', ['produit' => $produit]);

    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function retrieveProduits($categorie, $searchField) {
        return $this->em->getRepository(Produit::class)->findWithCriteria($categorie, $searchField);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function retrieveProduitFromCategorie($categorie) {
        return $this->em->getRepository(Categorie::class)->find($categorie)->getProduits();
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function retrieveAllCategories() 
    {
        return $this->em->getRepository(Categorie::class)->findAll();
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function retrieveAllProduits() 
    {

        return $this->em->getRepository(Produit::class)->findAll();
        
    }
}
