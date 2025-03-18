<?php

namespace App\Controller;
//-----------------------------------
//    Fichier : HomeController.php
//    Par:      Anthony Grenier
//    Date :    2025-2-22
//-----------------------------------

use App\Classes\Panier;
use App\Classes\ProduitPanier;

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        $session = $request->getSession();

        $panier = $session->get('panier', new Panier()); 
        $session->set('panier', $panier);

        $request->getSession()->set('panier', $panier);
        
        if ($panier === null) {
            $panier = new Panier();
            $session->set('panier', $panier);
        }
        $nbItem = $panier->compterProduitsTotal();
        


        return $this->render('home/index.html.twig', ['produits' => $produits, 'categories' => $categories, 'nbItem'=> $nbItem]);
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

    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart($id, Request $request, ManagerRegistry $doctrine): RedirectResponse
    {
        $this->em = $doctrine->getManager();
    
        $session = $request->getSession();
        
        $panier = $session->get('panier', new Panier()); 

        $produit = $this->em->getRepository(Produit::class)->find($id);
    
    
        //si le produit existe on incremente sinon on le créer
        if ($panier->existenceProduitParId($produit->getId())) {
            $panier->incrementerQuantiteProduit($produit->getId());
        } else {
            $produitPanier = new ProduitPanier();
            $produitPanier->id = $produit->getId();
            $produitPanier->nom = $produit->getNom();
            $produitPanier->prix = $produit->getPrix();
            $produitPanier->quantiteCommande = 1; 

            $panier->ajouterProduit($produitPanier);
        }

        $session->set('panier', $panier);
    
    
        $produitsDansPanier = $panier->panier; 
    
        return $this->redirectToRoute('app_home');
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
