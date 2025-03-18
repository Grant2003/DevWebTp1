<?php

namespace App\Controller;

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

        $itemCount = $panier->compterProduitsTotal();

        $fraisDePort = 10;
        $totalSum = $panier->calculerSommePrix();
        $tps = ($totalSum+10)*0.05;
        $tvq =($totalSum+10)*0.0975;
        $total = $totalSum + $tps + $tvq + $fraisDePort;
        return $this->render('panier/panier.html.twig', [
            'items' => $items,
            'itemCount' => $itemCount,
            'totalSum' => $totalSum,
            'fraisDePort' => $fraisDePort,
            'tps' => $tps,
            'tvq' => $tvq,
            'total' => $total
        ]);
    }

    #[Route(path: '/delete-item/{id}', name: 'delete_item',methods: ['POST'])]

    public function supprimerProduitParId(Request $request, int $id): Response {
        $panier = $request->getSession()->get('panier', new Panier());
        $panier->supprimerProduitParId($id);

        return $this->redirectToRoute('route_panier');
    }
    #[Route('/updateall', name: 'update_all', methods: ['POST'])]
public function updateAll(Request $request): Response
{
    $submittedData = $request->request->all();

    $panier = $request->getSession()->get('panier', new Panier());

    if (isset($submittedData['quantiteCommande'])) {
        foreach ($submittedData['quantiteCommande'] as $id => $quantity) {
            $panier->majQuantiteProduit($id, (int) $quantity);  
        }
    }

    $request->getSession()->set('panier', $panier);

    return $this->redirectToRoute('route_panier');
}


    #[Route(path: '/viderPanier', name: 'vider_panier',methods: ['POST'])]

    public function ViderPanier(Request $request): Response {
        $panier = $request->getSession()->get('panier', new Panier());
        $panier = $panier->viderPanier();


        return $this->redirectToRoute('route_panier');
    }
    

}

                                                                                                                                                                