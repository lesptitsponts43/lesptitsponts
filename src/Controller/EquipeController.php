<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActualiteRepository;
use App\Repository\ProduitRepository;
use App\Entity\Actualite;

class EquipeController extends AbstractController
{
    #[Route('/equipe', name: 'equipe')]
    public function equipe(ActualiteRepository $actualiteRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
        ]);
    }
}
