<?php

namespace App\Controller;

//-----------------------------------
//   Fichier : PanierController.php
//   Par:      Anthony Grenier
//   Date :    2025-3-16
//-----------------------------------

use App\Classes\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route(path: '/panier', name: 'route_panier')]
    public function index(Request $request): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());

        $items = $panier->panier;

        //pour le compteur du panier
        $nbItem = $panier->compterProduitsTotal();

        //propriétés du tableau
        $fraisDePort = 10;
        $totalAvantTaxes = $panier->calculerSommePrix();
        $tps = ($totalAvantTaxes+10)*0.05;
        $tvq =($totalAvantTaxes+10)*0.0975;
        $total = $totalAvantTaxes + $tps + $tvq + $fraisDePort;

        return $this->render('panier/panier.html.twig', [
            'items' => $items,
            'totalAvantTaxes' => $totalAvantTaxes,
            'fraisDePort' => $fraisDePort,
            'tps' => $tps,
            'tvq' => $tvq,
            'total' => $total,
            'nbItem' => $nbItem
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    #[Route(path: '/delete-item/{id}', name: 'delete_item',methods: ['POST'])]

    public function supprimerProduitParId(Request $request, int $id): Response {

        $panier = $request->getSession()->get('panier', new Panier());
        $panier->supprimerProduitParId($id);
        $request->getSession()->set('panier', $panier);
        return $this->redirectToRoute('route_panier');
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/updateall', name: 'update_all', methods: ['POST'])]
    public function updateAll(Request $request): Response
    {
        $submittedData = $request->request->all();

        $panier = $request->getSession()->get('panier', new Panier());

        //pour chaque item du panier on change la quantité pour la quantité choisie grace a l'id et la quantité du form
        if (isset($submittedData['quantiteCommande'])) {
            foreach ($submittedData['quantiteCommande'] as $id => $quantite) {
              $panier->majQuantiteProduit($id, (int) $quantite);  
            }
        }

        $request->getSession()->set('panier', $panier);

        return $this->redirectToRoute('route_panier');
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    #[Route(path: '/viderPanier', name: 'vider_panier',methods: ['POST'])]

    public function ViderPanier(Request $request): Response {
        $panier = $request->getSession()->get('panier', new Panier());
        $panier = $panier->viderPanier();


        return $this->redirectToRoute('route_panier');
    }
    

}

                                                                                                                                                                