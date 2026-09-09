<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActualiteRepository;
use App\Repository\ValeurRepository;
use App\Repository\BlocAccueilRepository;
use App\Repository\ProduitRepository;
use App\Repository\GalerieRepository;
use App\Entity\Actualite;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ActualiteRepository $actualiteRepository,ValeurRepository $valeurRepository,BlocAccueilRepository $BlocAccueilRepository,GalerieRepository $GalerieRepository): Response
    {
        $actualites = $actualiteRepository->findBy(
            [],
            ['date' => 'DESC'],
            3
        );
        $valeurs = $valeurRepository->findBy(
            [],
            [],
            3
        );
        $blocAccueil = $BlocAccueilRepository->findOneBy([]);

        $galeries= $GalerieRepository->findBy(
            [],
            [],
            6
        );

        return $this->render('main/home.html.twig', [
            'actualites' => $actualites,
            'valeurs' => $valeurs,
            'blocAccueil' => $blocAccueil,
            'galeries' => $galeries,
        ]);
    }

    #[Route('/mentions', name: 'mentions')]
    public function mentions(): Response
    {
         return $this->render('main/mentions.html.twig', [
        ]);
    }
  
}
