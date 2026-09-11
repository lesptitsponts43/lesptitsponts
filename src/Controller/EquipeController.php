<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActualiteRepository;
use App\Repository\ProduitRepository;
use App\Repository\ValeurRepository;
use App\Entity\Actualite;

class EquipeController extends AbstractController
{
    #[Route('/equipe', name: 'equipe')]
    public function equipe(ActualiteRepository $actualiteRepository,ValeurRepository $valeurRepository): Response
    {
        $valeurs = $valeurRepository->findBy(
            [],
            [],
            3
        );
        return $this->render('equipe/index.html.twig', [
            'valeurs' => $valeurs,
        ]);
    }
}
