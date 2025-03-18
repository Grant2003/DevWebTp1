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


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'route_contact')]
    public function index(Request $request): Response
    {
        $panier = $request->getSession()->get('panier', new Panier());
        $itemCount = $panier->compterProduitsTotal();

        return $this->render('contact/index.html.twig', ['nbItem'=>$itemCount
        ]);
    }
}
