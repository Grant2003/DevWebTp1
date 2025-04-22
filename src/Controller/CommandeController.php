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
        // Cart is assumed to be stored in session
    
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
            $detail->setQuantiteRupture($item->quantiteCommande);

            $detail->setCommande($commande);
        
            $em->persist($detail);
        }
        $em->persist($commande);
        $em->flush();

        //propriétés du tableau
        $fraisDePort = 10;
        $totalAvantTaxes = $panier->calculerSommePrix();
        $tps = ($totalAvantTaxes+10)*0.05;
        $tvq =($totalAvantTaxes+10)*0.0975;
        $total = $totalAvantTaxes + $tps + $tvq + $fraisDePort;

        $request->getSession()->remove('panier');
        $this->addFlash('success', 'Votre commande a été enregistrée.');
        $commandetest = $em->getRepository(Commande::class)->find(99);
        return $this->render('Commande/index.html.twig', [
            'details' => $commandetest->getCommandeDetails(),
            'totalAvantTaxes' => $totalAvantTaxes,
            'fraisDePort' => $fraisDePort,
            'tps' => $tps,
            'tvq' => $tvq,
            'total' => $total,
            'nbItem' => $itemCount
        ]);   
    }
    
}
