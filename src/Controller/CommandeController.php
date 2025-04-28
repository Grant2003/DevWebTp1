<?php

namespace App\Controller;

//-----------------------------------
//   Fichier : CommandeController.php
//   Par:      Anthony Grenier
//   Date :    2025-4-20
//-----------------------------------

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Classes\Panier;
use App\Entity\Commande;
use App\Entity\Client;
use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;


use App\Entity\CommandeDetail;



class CommandeController extends AbstractController
{
    #[Route('/commander', name: 'route_commander', methods: ['POST'])]
    public function commander(Request $request,ManagerRegistry $doctrine): Response
    {
        //affichage des données du panier avant la creation de la commande pour confirmation
        $panier = $request->getSession()->get('panier', new Panier());
        $fraisDePort = 10;
        $totalAvantTaxes = $panier->calculerSommePrix();
        $tps = ($totalAvantTaxes+10)*0.05;
        $tvq =($totalAvantTaxes+10)*0.0975;
        $total = $totalAvantTaxes + $tps + $tvq + $fraisDePort;
        $items = $panier->panier;
        $clientSession = $request->getSession()->get('utilisateurConnecte');

        //gestion d'injection
        if($clientSession == null){
            $this->addFlash('warning', 'Utilisateur Déconnecté...');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Commande/commande.html.twig', [
            'items' => $items,
            'totalAvantTaxes' => $totalAvantTaxes,
            'fraisDePort' => $fraisDePort,
            'tps' => $tps,
            'tvq' => $tvq,
            'total' => $total,
        ]);   
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/paiement', name: 'route_paiement', methods: ['POST'])]
    public function paiement(Request $request,ManagerRegistry $doctrine): Response
    {
        $clientSession = $request->getSession()->get('utilisateurConnecte');

        //gestion d'injection
        if($clientSession == null){
            $this->addFlash('warning', 'Utilisateur Déconnecté...');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Commande/paiement.html.twig', [
        ]);   
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/confirmation', name: 'confirmation', methods: ['POST'])]
    public function confirmation(Request $request,ManagerRegistry $doctrine): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $clientSession = $request->getSession()->get('utilisateurConnecte');

        //gestion d'injection
        if($clientSession == null){
            $this->addFlash('warning', 'Utilisateur Déconnecté...');

            return $this->redirectToRoute('app_home');
        }

        $client = $doctrine->getRepository(Client::class)->find($clientSession->getUtilisateur());

        
        $em = $doctrine->getManager();
    
        //gestion d'injection
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('route_panier');
        }
    
        //creation de la commande
        $commande = new Commande();
        $commande->setClient($client);
        $commande->setDateCommande(new \DateTime());

        //creation des details pour chaque produits du panier
        foreach ($panier->panier as $item) {
            $produit = $em->getRepository(Produit::class)->find($item->id);
        
            //on saute l'item si le produit n'existe pas pour eviter les crash(gestion d'erreur dans un cas eventuel)
            if (!$produit) {
                continue;
            }
        
            $detail = new CommandeDetail();
            //on met le produit dans le detail
            $detail->setProduit($produit);
            //si le produit est en rupture, on ajoute dans qtte rupture et met un flash
            if($produit->getQtte_Stock() - $item->quantiteCommande < 0){

                $detail->setQuantiteRupture($item->quantiteCommande - $produit->getQtte_Stock());
                $detail->setQuantite($item->quantiteCommande);
                
                $produit->setQtteStock(0);
                $this->addFlash('warning', 'Attention: rupture de stock pour '.$item->nom.' (Manque '.$detail->getQuantiteRupture().' items)');

                $em->persist($produit);
            }
            else{
                $detail->setQuantiteRupture(0);
                $detail->setQuantite($item->quantiteCommande);

                $produit->setQtteStock($produit->getQtte_Stock() - $item->quantiteCommande);
                $em->persist($produit);
            }

            $detail->setCommande($commande);
        
            $em->persist($detail);
        }
        //insertion de la commande
        $em->persist($commande);
        $em->flush();
        //on enleve le panier
        $request->getSession()->remove('panier');
        //mis a jour pour l<interface
        $request->getSession()->set('nbItem', 0);

        $this->addFlash('success', 'Commande '.$commande->getIdCommande().' En préparation');


        //attributs pour la confirmation
        $noCommande = $commande->getIdCommande();
        $client = $commande->getClient();
        $adresse = $client->getAdresse();
        $ville = $client->getVille();
        $province = $client->getProvince();
        $codePostal = $client->getCodePostal();
        $email = $client->getEmail();

        $total = $commande->total();
        
        return $this->render('Commande/confirmation.html.twig', [
            'noCommande' => $noCommande,
            'adresse' => $adresse,
            'ville' => $ville,
            'province' => $province,
            'codePostal' => $codePostal,
            'email' => $email,
            'total' => $total
        ]);   
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/historique', name: 'route_historique', methods: ['GET'])]
    public function historique(Request $request,ManagerRegistry $doctrine): Response
    {
        $clientSession = $request->getSession()->get('utilisateurConnecte');

        //gestion d'injection
        if($clientSession == null){
            $this->addFlash('warning', 'Utilisateur Déconnecté...');

            return $this->redirectToRoute('app_home');
        }

        $commandes = $doctrine->getRepository(Commande::class)->findByClientOrdered($clientSession);




        return $this->render('Commande/historique.html.twig', [
            'commandes'=> $commandes
        ]); 
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/annuler-commande{id}', name: 'route_annuler_commande', methods: ['GET'])]

    public function annulerCommande(int $id, Request $request,ManagerRegistry $doctrine)
    {
        $commande = $doctrine->getRepository(Commande::class)->find($id);
        $clientSession = $request->getSession()->get('utilisateurConnecte');

        //gestion d'injection
        if($clientSession == null){
            $this->addFlash('warning', 'Utilisateur Déconnecté...');

            return $this->redirectToRoute('app_home');
        }
        if (!$commande) {
            $this->addFlash('warning', 'Commande non trouvée!');

            return $this->redirectToRoute('route_historique');        }

        return $this->render('commande/confirmation_annulation.html.twig', [
            'commande' => $commande
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/confirmer-annulation/{id}', name: 'route_confirmer_annulation', methods: ['POST'])]

    public function confirmerAnnulation(int $id,ManagerRegistry $doctrine,Request $request)
    {
        //recuperation de la commande
        $commande = $doctrine->getRepository(Commande::class)->find($id);

        //gestion d'injection
        if(!$commande){
            $this->addFlash('warning', 'Commande non trouvée!');

            return $this->redirectToRoute('route_historique');
        }
        $clientSession = $request->getSession()->get('utilisateurConnecte');
        $clientCommande = $commande->getClient();
        //gestion d'injection

        if($clientSession == null){
            $this->addFlash('warning', 'Utilisateur Déconnecté...');

            return $this->redirectToRoute('app_home');
        }
        //gestion d'injection
        if ($clientCommande->getUtilisateur() != $clientSession->getUtilisateur()) {
            $this->addFlash('warning', 'Commande non trouvée!');

            return $this->redirectToRoute('route_historique');
        }

        //on enleve la commande
        $em = $doctrine->getManager();
        $em->remove($commande);
        $em->flush();

        // Réapprovisionner les produits
        foreach ($commande->getCommandeDetails() as $detail) {
            $produit = $detail->getProduit();
            $produit->getQtte_Stock($produit->getQtte_Stock() + ($detail->getQuantite() - $detail->getQuantiteRupture()));
            $em->persist($produit);
            
        }
        $em->flush();

        return $this->redirectToRoute('route_historique');
    }
}
