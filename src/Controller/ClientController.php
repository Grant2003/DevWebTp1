<?php

namespace App\Controller;

//-----------------------------------
//   Fichier : PanierController.php
//   Par:      Anthony Grenier
//   Date :    2025-3-16
//-----------------------------------
use App\Classes\Panier;
use App\Classes\ProduitPanier;
use App\Entity\Client;
use App\Form\ClientType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ClientController extends AbstractController
{
    #[Route(path: '/connexion', name: 'route_connexion')]
    public function index(Request $request): Response
    {

        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        return $this->render('client/connexion.html.twig', ['nbItem'=>$itemCount
        ]);

    }

    // public function creerCompte(Request $request): Response
    // {



    //     $panier = $request->getSession()->get('panier', new Panier());
    //     $itemCount = $panier->compterProduitsTotal();

    //     return $this->render('client/creerCompte.html.twig', ['nbItem'=>$itemCount
    //     ]);
    // }
    #[Route(path: '/creercompte', name: 'route_creer')]

    public function creerCompte(Request $request, ValidatorInterface $validator): Response
    {
        $utilisateur = $request->getSession()->get('utilisateur', new Client());
        $form = $this->createForm(ClientType::class, $utilisateur);
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $request->getSession()->set('utilisateur', $utilisateur);
                return $this->render('Client/confirmation.html.twig', [
                    'nbItem' => $itemCount,
                    'utilisateur' => $utilisateur,
                ]);
            }
        }

        return $this->render('Client/creerCompte.html.twig', [
            'nbItem' => $itemCount,
            'form' => $form->createView(),
        ]);
    }
    #[Route(path: '/confirmer', name: 'route_confirmer')]

    public function confirmer(Request $request, ManagerRegistry $doctrine): Response
    {
        $utilisateur = $request->getSession()->get('utilisateur', new Client());
        $em  = $doctrine->getManager();

        $em->persist($utilisateur);
        $em->flush();
        $request->getSession()->set('utilisateurConnecte', $utilisateur);

        return $this->redirectToRoute('app_home'); 
    }
    #[Route(path: '/deconnexion', name: 'route_deconnexion')]

    public function logout(Request $request): Response
    {
        $request->getSession()->remove('utilisateurConnecte');

        return $this->redirectToRoute('app_home'); // Or wherever you want to redirect after logout
    }  
    #[Route(path: '/modifierCompte', name: 'route_modifier')]

    public function modiferCompte(Request $request, ValidatorInterface $validator): Response
    {
        $utilisateur = $request->getSession()->get('utilisateurConnecte', new Client());
        $form = $this->createForm(ClientType::class, $utilisateur);
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $request->getSession()->set('utilisateur', $utilisateur);
                return $this->render('Client/confirmation.html.twig', [
                    'nbItem' => $itemCount,
                    'utilisateur' => $utilisateur,
                ]);
            }
        }

        return $this->render('Client/modifier.html.twig', [
            'nbItem' => $itemCount,
            'form' => $form->createView(),
        ]);
    } 


}