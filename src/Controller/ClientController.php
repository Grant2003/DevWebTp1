<?php

namespace App\Controller;

//-----------------------------------
//   Fichier : PanierController.php
//   Par:      Anthony Grenier
//   Date :    2025-3-16
//-----------------------------------
use App\Classes\Panier;
use App\Classes\ProduitPanier;
use App\Classes\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route(path: '/connexion', name: 'route_connexion')]
    public function index(Request $request): Response
    {

        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        return $this->render('contact/index.html.twig', ['nbItem'=>$itemCount
        ]);
        return $this->render('client/index.html.twig', [

        ]);
    }
}