<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServicesController extends AbstractController
{
    #[Route('/services', name: 'services')]
    public function liste_service(): Response
    {
        return $this->render('services/liste.html.twig');
    }


    #[Route('/services/JUI457M', name: 'voir-service')]
    public function voir_service(): Response
    {
        return $this->render('services/voir.html.twig');
    }

    #[Route('/panier', name: 'panier')]
    public function panier(): Response
    {
        return $this->render('services/panier.html.twig');
    }


    #[Route('/publier', name: 'publier')]
    public function publier(): Response
    {
        return $this->render('services/publier.html.twig');
    }
}
