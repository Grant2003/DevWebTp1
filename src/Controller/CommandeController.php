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
use App\Entity\CommandeDetail;



class CommandeController extends AbstractController
{
    #[Route('/commander', name: 'commander', methods: ['POST'])]
    public function commander(Request $request): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();
        // Cart is assumed to be stored in session
    
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('panier');
        }
    
        $commande = new Commande();
        $commande->setClient($client);
        $commande->setDateCommande(new \DateTime());
    
        foreach ($panier as $item) {
            $produit = $em->getRepository(Produit::class)->find($item['id']);
            if (!$produit) {
                continue;
            }
    
            $detail = new CommandeDetail();
            $detail->setProduit($produit);
            $detail->setQuantite($item['quantite']);
            $detail->setCommande($commande);
    
            $em->persist($detail);
        }
    
        $em->persist($commande);
        $em->flush();
    
        $session->remove('panier');
        $this->addFlash('success', 'Votre commande a été enregistrée.');
    
        return $this->redirectToRoute('app_home');
    }
    
}
