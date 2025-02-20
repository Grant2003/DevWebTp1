<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\Categories;
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
        //$entityManager = $doctrine->getManager(); 
        //! DANGER Ligne importante pour les fonctions utilitaires
        $this->em = $doctrine->getManager();

        $role = $request->query->get('role'); // $_GET['role']
        $searchField = $request->request->get('search_field'); // $_POST['search_field']

        $categories = $this->retrieveAllCategories();

        //$champions = $this->retrieveAllChampions($entityManager);
       
        // if($role != null) {
        //     $champions = $this->retrieveChampionFromRole($role);
        // } else {
        //     $champions = $this->retrieveAllChampions();
        // }
        
        $produits = $this->retrieveProducts($role, $searchField);
        

        //Pour déboguer des fois
        //var_dump($champions);

        return $this->render('home/index.html.twig', ['produits' => $produits, 'categories' => $categories]);
    }


    #[Route('/produuits/{id}', name:'produit_modal')]
    public function infoChampion($idChampion, Request $request, ManagerRegistry $doctrine): Response {
        //2 Philosophies -> JSON, HTML

        $this->em = $doctrine->getManager();

        $champion = $this->em->getRepository(Champion::class)->find($idChampion);

        return $this->render('home/produit.modal.twig', ['produit' => $produit]);

    }

    
    private function retrieveChampions($categorie, $searchField) {
        return $this->em->getRepository(Produit::class)->findWithCriteria($categorie, $searchField);
    }

    private function retrieveProduitFromCategorie($categorie) {
        return $this->em->getRepository(Categorie::class)->find($categorie)->getProduits();
    }

    private function retrieveAllCategories() 
    {
        //SQL -> SELECT * FROM roles
        return $this->em->getRepository(Categorie::class)->findAll();
    }

    private function retrieveAllProduits() 
    {

        return $this->em->getRepository(Produit::class)->findAll();
        
    }

    // private function retrieveAllChampions($entityManager) {

    //     return $entityManager->getRepository(Champion::class)->findAll();
        
    // }

    // /yannick
    #[Route('/yannick', name:'yannick.route')]
    public function yannickRoute() : Response {

        return $this->render('home/yannick.html.twig', [
            'image_name' => 'chat2',
            'image_extension' => 'webp'
        ]);
     }
}
