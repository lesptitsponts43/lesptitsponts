<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActualiteRepository;
use App\Repository\ProduitRepository;
use App\Entity\Actualite;

class EvenementController extends AbstractController
{
     #[Route('/news', name: 'news')]
    public function news(ActualiteRepository $actualiteRepository): Response
    {
        $actualites = $actualiteRepository->findBy(
            [],
            ['date' => 'DESC'] 
        );
        return $this->render('evenements/index.html.twig', [
            'actualites' => $actualites
        ]);
    }
     #[Route('/news/{id}', name: 'news_show')]
    public function show(Actualite $actualite): Response
    {
        
        return $this->render('evenements/show.html.twig', [
            'actualite' => $actualite
        ]);
    }
}
