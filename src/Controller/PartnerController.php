<?php
namespace App\Controller;

use App\Repository\PartenaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartnerController extends AbstractController
{
    public function partners(PartenaireRepository $PartenaireRepository): Response
    {
        $partners = $PartenaireRepository->findAll();

        return $this->render('partenaires/index.html.twig', [
            'title' => 'Nos partenaires',
            'partenaires' => $partners
        ]);
    }
}
