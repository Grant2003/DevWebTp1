<?php

namespace App\Controller;

//-----------------------------------
//   Fichier : ClientController.php
//   Par:      Anthony Grenier
//   Date :    2025-3-29
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route(path: '/creercompte', name: 'route_creer')]

    public function creerCompte(Request $request, ValidatorInterface $validator): Response
    {
        $utilisateur = $request->getSession()->get('utilisateur', new Client());
        $form = $this->createForm(ClientType::class, $utilisateur);

        $form->handleRequest($request);

        //gestion du form et redirection vers la confirmation
        if ($form->isSubmitted() && $form->isValid()) {
                $request->getSession()->set('utilisateur', $utilisateur);
                return $this->render('Client/confirmation.html.twig', [
                    'utilisateur' => $utilisateur,
                ]);
            
        }

        return $this->render('Client/creerCompte.html.twig', [
            'form' => $form->createView(),
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route(path: '/confirmer', name: 'route_confirmer')]

    public function confirmer(Request $request, ManagerRegistry $doctrine): Response
    {
        $utilisateur = $request->getSession()->get('utilisateur', new Client());
        $em  = $doctrine->getManager();

        $em->persist($utilisateur);
        $em->flush();
        $request->getSession()->set('utilisateurConnecte', $utilisateur);
        $request->getSession()->remove('utilisateur');

        $this->addFlash('success', 'Creation du compte réussie!');
        return $this->redirectToRoute('app_home'); 
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route(path: '/deconnexion', name: 'route_deconnexion')]

    public function logout(Request $request): Response
    {
        $request->getSession()->remove('utilisateurConnecte');
        $this->addFlash('success', 'Déconnexion réussie!');

        return $this->redirectToRoute('app_home'); 
    } 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    #[Route(path: '/modifierCompte', name: 'route_modifier')]

    public function modifierCompte(Request $request,ManagerRegistry $doctrine, FormFactoryInterface $formFactory)
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        $session = $request->getSession();
        //si la session ne contient pas d'utilisteur (en cas de retour en arriere apres deconnexion) on redirige vers la page de connexion pour eviter un plantage.
        if(!$session->has('utilisateurConnecte')){
            $this->addFlash('error', 'Aucun compte actif pour la modification, veuillez vous connecter');
            return $this->render('Client/connexion.html.twig', ['nbItem' => $itemCount]);
        }
        $utilisateurSession = $session->get('utilisateurConnecte', new Client());
        //recuperation du user pour pouvoir le gerer avec doctrine
        $utilisateur = $doctrine->getRepository(Client::class)->find($utilisateurSession->getUtilisateur());
        $ancienMDP = $utilisateur->getMotDePasse();

        $em  = $doctrine->getManager();

        //les createNamed permette a symfony de differencier les deux click des bouttons et de bien valider un seul form
        $generalForm = $formFactory->createNamed('general_form', ClientType::class, $utilisateur, [
            'is_modify' => true,
        ]);        

        $generalForm->handleRequest($request);

        $passwordForm = $formFactory->createNamed('password_form', ClientType::class, $utilisateur, ['is_password' => true,
        ]);

        $passwordForm->handleRequest($request);
        
        if ($request->isMethod('POST')) {
            //formulaire generale
            if ($generalForm->isSubmitted() && $generalForm->isValid() ) {
                $em->flush();
                $this->addFlash('success', 'Informations mises à jour !');
            }
            //verrification pour le formulaire de mdp
            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                //je verifie ici pour etre bien sure mais le test ce fait deja niveau serveur.
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
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route(path: '/connexion', name: 'route_connexion')]
    public function Connexion(Request $request, ManagerRegistry $doctrine): Response
    {
        $defaultRedirect = 'app_home';
        $from = $request->query->get('redirect', $defaultRedirect);
        if ($request->isMethod('POST')) {

            $username = $request->request->get('user');  
            $password = $request->request->get('mdp');  
            $from     = $request->request->get('redirect', $defaultRedirect);

            $em = $doctrine->getManager();
            $utilisateur = $em->getRepository(Client::class)->findOneBy(['utilisateur' => $username]);
        
            if ($utilisateur && $utilisateur->getMotDePasse() === $password) {
                $request->getSession()->set('utilisateurConnecte', $utilisateur);
                $this->addFlash('success', 'Connexion réussie!');
        
                if ($request->getSession()->get('target_after_login')!=null) {
                    $request->getSession()->remove('target_after_login');
                    return $this->redirectToRoute('route_commander');
                }
        
                return $this->redirectToRoute('app_home');
            }
        
            $this->addFlash('error', 'Combinaison de connexion invalide');
        }
        

        return $this->render('Client/connexion.html.twig', [
            'redirect' => $from, 
        ]);
    }


}