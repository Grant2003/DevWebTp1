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

    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart($id, Request $request, ManagerRegistry $doctrine): RedirectResponse
    {
        $this->em = $doctrine->getManager();
    
        // Access the session to manage the cart
        $session = $request->getSession();
        
        // Retrieve the existing cart from the session (if any)
        $panier = $session->get('panier', new Panier()); // Don't reinitialize the cart

        // Fetch the product from the database using the provided ID
        $produit = $this->em->getRepository(Produit::class)->find($id);
    
        if ($produit) {
            if ($panier->existenceProduitParId($produit->getId())) {
                $panier->incrementerQuantiteProduit($produit->getId());
                $this->addFlash('success', 'Produit ajouté au panier(i)!');
            } else {
                // Create a new ProduitPanier object and add it to the cart
                $produitPanier = new ProduitPanier();
                $produitPanier->id = $produit->getId();
                $produitPanier->nom = $produit->getNom();
                $produitPanier->prix = $produit->getPrix();
                $produitPanier->quantiteCommande = 1; // Default to 1 when adding for the first time
                $this->addFlash('success', 'Produit ajouté au panier!');
    
                // Add the new product to the panier
                $panier->ajouterProduit($produitPanier);
            }
    
            // Save the updated cart back into the session
            $session->set('panier', $panier);
        } else {
            $this->addFlash('error', 'Produit non trouvé.');
        }
    
        // Access the products in the panier (the array of ProduitPanier objects)
        $produitsDansPanier = $panier->panier; // Access the panier array directly

    
        // Add a flash message for each product in the panier
        foreach ($produitsDansPanier as $produitPanier) {
            // Ensure you're working with an instance of ProduitPanier and access its properties
            if ($produitPanier instanceof ProduitPanier) {
                $this->addFlash('info', 'Produit dans le panier: ' . $produitPanier->nom . ' - Quantité: ' . $produitPanier->quantiteCommande);
            }
        }
    
        // Redirect back to the home page (or you can redirect to the cart page)
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
