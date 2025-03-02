<?php

namespace App\Controller;

//-----------------------------------
//   Fichier : ContactController.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
//-----------------------------------

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'route_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
        ]);
    }
}
