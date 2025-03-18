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
        // Retrieve the panier (cart) from the session, or create a new one if it doesn't exist
        $panier = $request->getSession()->get('panier', new Panier());

        // Get the list of items in the panier
        $items = $panier->panier;

        // Calculate the total number of items in the panier
        $itemCount = $panier->compterProduitsTotal();

        // Calculate the total price of the items in the panier
        $totalSum = $panier->calculerSommePrix();

        // Render the template and pass the items, itemCount, and totalSum to it
        return $this->render('panier/panier.html.twig', [
            'items' => $items,
            'itemCount' => $itemCount,
            'totalSum' => $totalSum,
        ]);
    }

    #[Route(path: '/delete-item/{id}', name: 'delete_item',methods: ['POST'])]

    public function supprimerProduitParId(Request $request, int $id): Response {
        $panier = $request->getSession()->get('panier', new Panier());
        $panier->supprimerProduitParId($id);

    // Logic to remove the item from the panier array
        return $this->redirectToRoute('route_panier');
    }
    #[Route('/updateall', name: 'update_all', methods: ['POST'])]
public function updateAll(Request $request): Response
{
    // Extract the data sent from the form
    $submittedData = $request->request->all();

    // Get the current panier from the session
    $panier = $request->getSession()->get('panier', new Panier());

    // Check if 'quantiteCommande' is in the submitted data
    if (isset($submittedData['quantiteCommande'])) {
        foreach ($submittedData['quantiteCommande'] as $id => $quantity) {
            // Update the product quantity in the cart
            $panier->majQuantiteProduit($id, (int) $quantity);  // Ensure quantity is an integer
        }
    }

    // Store the updated panier back in the session
    $request->getSession()->set('panier', $panier);

    // Redirect back to the panier page (or to a specific route if needed)
    return $this->redirectToRoute('route_panier');
}


    #[Route(path: '/viderPanier', name: 'vider_panier',methods: ['POST'])]

    public function ViderPanier(Request $request): Response {
        $panier = $request->getSession()->get('panier', new Panier());
        $panier = $panier->viderPanier();


    // Logic to remove the item from the panier array
        return $this->redirectToRoute('route_panier');
    }
    

}

                                                                                                                                                                