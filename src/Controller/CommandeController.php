<?php

namespace App\Controller;

//-----------------------------------
//   Fichier : ContactController.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
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
    #[Route('/commander', name: 'commander', methods: ['POST'])]
    public function commander(Request $request,ManagerRegistry $doctrine): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $clientSession = $request->getSession()->get('utilisateurConnecte');
        $client = $doctrine->getRepository(Client::class)->find($clientSession->getUtilisateur());
        $em = $doctrine->getManager();

        $itemCount = $panier->compterProduitsTotal();

        //propriétés du tableau
        $fraisDePort = 10;
        $totalAvantTaxes = $panier->calculerSommePrix();
        $tps = ($totalAvantTaxes+10)*0.05;
        $tvq =($totalAvantTaxes+10)*0.0975;
        $total = $totalAvantTaxes + $tps + $tvq + $fraisDePort;
        $items = $panier->panier;

        return $this->render('Commande/commande.html.twig', [
            'items' => $items,
            'totalAvantTaxes' => $totalAvantTaxes,
            'fraisDePort' => $fraisDePort,
            'tps' => $tps,
            'tvq' => $tvq,
            'total' => $total,
            'nbItem' => $itemCount
        ]);   
    }
    #[Route('/paiement', name: 'paiement', methods: ['POST'])]
    public function paiement(Request $request,ManagerRegistry $doctrine): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        return $this->render('Commande/paiement.html.twig', [
            'nbItem' => $itemCount
        ]);   
    }
    #[Route('/confirmation', name: 'confirmation', methods: ['POST'])]
    public function confirmation(Request $request,ManagerRegistry $doctrine): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $clientSession = $request->getSession()->get('utilisateurConnecte');
        $client = $doctrine->getRepository(Client::class)->find($clientSession->getUtilisateur());
        $em = $doctrine->getManager();
    
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('panier');
        }
    
        $commande = new Commande();
        $commande->setClient($client);
        $commande->setDateCommande(new \DateTime());

        foreach ($panier->panier as $item) {
            $produit = $em->getRepository(Produit::class)->find($item->id);
        
            if (!$produit) {
                continue;
            }
        
            $detail = new CommandeDetail();
            $detail->setProduit($produit);
            $detail->setQuantite($item->quantiteCommande);
            if($produit->getQtte_Stock() - $item->quantiteCommande <= 0){
                $detail->setQuantiteRupture($item->quantiteCommande - $produit->getQtte_Stock());
                $produit->setQtteStock(0);
                $em->persist($produit);
            }
            else{
                $detail->setQuantiteRupture(0);

                $produit->setQtteStock($produit->getQtte_Stock() - $item->quantiteCommande);
                $em->persist($produit);
            }

            $detail->setCommande($commande);
        
            $em->persist($detail);
        }
        $em->persist($commande);
        $em->flush();
        $request->getSession()->remove('panier');


        $itemCount = $panier->compterProduitsTotal();
        $noCommande = $commande->getIdCommande();
        $client = $commande->getClient();
        $adresse = $client->getAdresse();
        $ville = $client->getVille();
        $province = $client->getProvince();
        $codePostal = $client->getCodePostal();
        $email = $client->getEmail();
        $fraisDePort = 10;
        $totalAvantTaxes = $panier->calculerSommePrix();
        $tps = ($totalAvantTaxes+10)*0.05;
        $tvq =($totalAvantTaxes+10)*0.0975;
        $total = $totalAvantTaxes + $tps + $tvq + $fraisDePort;
        
        return $this->render('Commande/confirmation.html.twig', [
            'nbItem' => $itemCount,
            'noCommande' => $noCommande,
            'adresse' => $adresse,
            'ville' => $ville,
            'province' => $province,
            'codePostal' => $codePostal,
            'email' => $email,
            'total' => $total
        ]);   
    }
}
