<?php
//-----------------------------------
//   Fichier : AdminController.php
//   Par:      Anthony Grenier
//   Date :    2025-3-29
//   Modification :    2025-5-07
//-----------------------------------
namespace App\Controller;
use App\Classes\ImageProduit;
use App\Entity\Client;
use App\Entity\Produit;
use App\Form\ImageProduitType;
use App\Form\ProduitType;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Entity\Commande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class AdminController extends AbstractController
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route(path: '/admin', name: 'route_connexionAdmin')]
    public function adminConnexion(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->isMethod('POST')) {

            $username = $request->request->get('user');  
            $password = $request->request->get('mdp');  

            $em = $doctrine->getManager();
            $utilisateur = $em->getRepository(Client::class)->findOneBy(['utilisateur' => $username]);
        
            //verification du mdp haché et connection seulement si le user est admin
            if ($utilisateur && password_verify($password, $utilisateur->getMotDePasse()) &&$username == 'admin') {
                $request->getSession()->set('adminConnecte', $utilisateur);
                $this->addFlash('success', 'Connexion réussie!');
        
                return $this->render('Admin/admin.html.twig', [
                ]);      
            }
        
            $this->addFlash('error', 'Combinaison de connexion invalide');
        }
        

        return $this->render('Admin/connexionAdmin.html.twig', [
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route(path: '/deconnexionAdmin', name: 'route_deconnexionAdmin')]
    public function deconnexionAdmin(Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $request->getSession()->remove('adminConnecte');
        $this->addFlash('success', 'Déconnexion réussie!');

        return $this->redirectToRoute('app_home'); 
    }
    #[Route(path: '/adminMenu', name: 'route_admin')]
    public function admin(Request $request, ManagerRegistry $doctrine): Response
    {
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        return $this->render('Admin/admin.html.twig', [
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route(path: '/ajouterCategorie', name: 'route_ajouter_categorie')]
    public function ajouterCategorie(Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }

        //creation de categorie avec un form categorie
        $em = $doctrine->getManager();
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès!');
            return $this->redirectToRoute('route_admin');
        }

        return $this->render('Admin/ajouterCategorie.html.twig', [
            'form' => $form->createView()
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //liste des produits a modifier
    #[Route('/modifierProduitListe', name: 'route_modifier_produit_liste')]
    public function list(Request $request,ManagerRegistry $doctrine): Response
    {
        //gestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }

        $em = $doctrine->getManager();

        $produits = $em->getRepository(Produit::class)->findAll();

        return $this->render('Admin/modifierProduitListe.html.twig', [
            'produits' => $produits
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //modification des categories avec un form
    #[Route(path: '/modifierCategorie', name: 'route_modifier_categorie')]

    public function modifierCategorie( Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $em = $doctrine->getManager();

        $categories = $em->getRepository(Categorie::class)->findAll();

        $form = $this->createFormBuilder(['categories' => $categories])
            ->add('categories', CollectionType::class, [
                'entry_type' => CategorieType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedData = $form->getData()['categories'];

            foreach ($updatedData as $categorie) {
                $em->persist($categorie);
            }

            $em->flush();
            $this->addFlash('success', 'Catégories mises à jour avec succès!');
            return $this->redirectToRoute('route_modifier_categorie');
        }

        return $this->render('Admin/modifierCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //ajouter un produit avec un form
    #[Route(path: '/ajouterProduit', name: 'route_ajouter_produit')]
    public function ajouterProduit( Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $em = $doctrine->getManager();
        
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès!');
            return $this->redirectToRoute('route_admin'); // Redirect to list or confirmation as needed
        }

        return $this->render('Admin/ajouterProduit.html.twig', [
            'form' => $form->createView()

        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //modification d'un produit avec un form

    #[Route(path: '/modifierProduit {id}', name: 'route_modifier_produit')]
    public function modifierProduit(int $id,Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $em = $doctrine->getManager();
        $produit = $em->getRepository(Produit::class)->find($id);

        //gestion d'injection avec un id inexistant
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès!');
            return $this->redirectToRoute('route_modifier_produit_liste');
        }

        return $this->render('Admin/modifierProduit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //liste des produits du catalogue
    #[Route(path: '/produitCatalogue', name: 'route_produits_catalogue')]
    public function produitCatalogue(Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection

        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }

        $em = $doctrine->getManager();

        $produits = $em->getRepository(Produit::class)->findAll();
        return $this->render('Admin/produitCatalogue.html.twig', [
            'produits'=>$produits
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //rapport des ventes
    #[Route(path: '/rapport', name: 'route_rapport')]
    public function rapport(Request $request, ManagerRegistry $doctrine): Response
    {

        //gestion d'injection

        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $commandes = $doctrine->getRepository(Commande::class)->findBy([], ['dateCommande' => 'DESC']);

        return $this->render('Admin/rapport.html.twig', [
            'commandes' =>$commandes
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //liste des produits a commander
    #[Route(path: '/aCommander', name: 'route_a_commander')]
    public function aCommander(Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection

        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $em = $doctrine->getManager();

        $produits = $em->getRepository(Produit::class)->findAll();

        return $this->render('Admin/aCommander.html.twig', [
            'produits' => $produits
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //modification des images pour le catalogue
    #[Route(path: '/modifierImageCatalogue {id}', name: 'route_image_catalogue')]
    public function modifierImageCatalogue(int $id,Request $request, ManagerRegistry $doctrine): Response
    {
        //gestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $em = $doctrine->getManager();
        $produit = $em->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }
        

        $imageProduit = new ImageProduit;
        $imageProduit->setProduitId($id);

        $formImageProduit = $this->createForm(ImageProduitType::class, $imageProduit);

        $formImageProduit->handleRequest($request);
        if ($formImageProduit->isSubmitted())
        {
            if ($formImageProduit->isValid())
            {
                $codeErr = 0;
                if ($imageProduit->televerserCatalogue($codeErre))
                {
                    $this->addFlash('succes', 'image téléversée avec succès!!');
                }
                else
                {
                    
                    $this->addFlash('erreur', "Erreur ($codeErr)lors du téléversement de l'image...");
                }
            }
        }


        return $this->render('Admin/modifierImageCatalogue.html.twig', [
            'produit' => $produit,
            'formImage' => $formImageProduit
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //modification des images de detail
    #[Route(path: '/modifierImageDetail {id}', name: 'route_image_detail')]
    public function modifierImageDetail(int $id,Request $request, ManagerRegistry $doctrine): Response
    {
        //geestion d'injection
        $session = $request->getSession();
        if(!$session->has('adminConnecte')){
            $this->addFlash('error', 'Aucun admin connecté, veuillez vous connecter');
            return $this->render('Admin/connexionAdmin.html.twig');
        }
        $em = $doctrine->getManager();
        $produit = $em->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }

        $imageProduit = new ImageProduit;
        $imageProduit->setProduitId($id);

        $formImageProduit = $this->createForm(ImageProduitType::class, $imageProduit);

        $formImageProduit->handleRequest($request);
        if ($formImageProduit->isSubmitted())
        {
            if ($formImageProduit->isValid())
            {
                $codeErr = 0;
                if ($imageProduit->televerserDetail($codeErre))
                {
                    $this->addFlash('succes', 'image téléversée avec succès!!');
                }
                else
                {
                    
                    $this->addFlash('erreur', "Erreur ($codeErr)lors du téléversement de l'image...");
                }
            }
        }


        return $this->render('Admin/modifierImageDetail.html.twig', [
            'produit' => $produit,
            'formImage' => $formImageProduit
        ]);
    }
}