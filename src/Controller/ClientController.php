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
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ClientController extends AbstractController
{

    #[Route(path: '/creercompte', name: 'route_creer')]

    public function creerCompte(Request $request, ValidatorInterface $validator): Response
    {
        $utilisateur = $request->getSession()->get('utilisateur', new Client());
        $form = $this->createForm(ClientType::class, $utilisateur);
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $request->getSession()->set('utilisateur', $utilisateur);
                return $this->render('Client/confirmation.html.twig', [
                    'nbItem' => $itemCount,
                    'utilisateur' => $utilisateur,
                ]);
            
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

        $this->addFlash('success', 'Creation du compte réussie!');
        return $this->redirectToRoute('app_home'); 
    }

    #[Route(path: '/deconnexion', name: 'route_deconnexion')]

    public function logout(Request $request): Response
    {
        $request->getSession()->remove('utilisateurConnecte');

        return $this->redirectToRoute('app_home'); 
    }  
    #[Route(path: '/modifierCompte', name: 'route_modifier')]


    #[Route('/compte/modifier/informations', name: 'modifier_informations')]
    public function modifierCompte(Request $request,ManagerRegistry $doctrine, FormFactoryInterface $formFactory)
    {
        $utilisateurSession = $request->getSession()->get('utilisateurConnecte', new Client());
        $utilisateur = $doctrine->getRepository(Client::class)->find($utilisateurSession->getUtilisateur());
        $ancienMDP = $utilisateur->getMotDePasse();
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();
        $em  = $doctrine->getManager();


        $generalForm = $formFactory->createNamed('general_form', ClientType::class, $utilisateur, [
            'is_modify' => true,
        ]);        

        $generalForm->handleRequest($request);

        $passwordForm = $formFactory->createNamed('password_form', ClientType::class, $utilisateur, ['is_password' => true,
        ]);

        $passwordForm->handleRequest($request);
        
        if ($request->isMethod('POST')) {

            if ($generalForm->isSubmitted() && $generalForm->isValid() ) {
                $em->flush();
                $this->addFlash('success', 'Informations mises à jour !');
            }

            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                if($ancienMDP === $utilisateur->getMotDePasse()){
                    $nouveauMdp = $passwordForm->get('neoMotDePasse')->getData();
                    $utilisateur->setMotDePasse($nouveauMdp);

                    $em->flush();
                    $this->addFlash('success', 'Mot de passe modifié !');
                }
                else{
                    $this->addFlash('error', 'Ancien mot de passe invalide');
                }
            }


        }
    
        return $this->render('Client/creerCompte.html.twig', [
            'generalInfoForm' => $generalForm->createView(),
            'passwordForm'  => $passwordForm->createView(),
            'nbItem' => $itemCount,
        ]);
    }
    
    #[Route(path: '/connexion', name: 'route_connexion')]
    public function Connexion(Request $request, ManagerRegistry $doctrine): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();
        if ($request->isMethod('POST')) {

            $username = $request->request->get('user');  
            $password = $request->request->get('mdp');  

            $em = $doctrine->getManager();
            $utilisateur = $em->getRepository(Client::class)->findOneBy(['utilisateur' => $username]);

            if ($utilisateur && $utilisateur->getMotDePasse() === $password) {

                $request->getSession()->set('utilisateurConnecte', $utilisateur);
                $this->addFlash('success', 'Connexion réussie!');

                return $this->redirectToRoute('app_home'); 
            }

            $this->addFlash('error', 'Combinaison de connexion invalide');
        }

        return $this->render('Client/connexion.html.twig', ['nbItem' => $itemCount]);
    }

}