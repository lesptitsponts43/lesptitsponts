<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

class TunnelController extends AbstractController
{
    #[Route('/products', name: 'products')]
    public function products(ProduitRepository $produitRepository): Response
    {
         $produits = $produitRepository->findBy(
            [],
            ['id' => 'DESC'] 
        );
        return $this->render('produit/index.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/order', name: 'order')]
    public function order(): Response
    {
        return $this->render('produit/order.html.twig');
    }
}
