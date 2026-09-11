<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * Ajouter un produit au panier
     */
    #[Route('/panier/ajouter/{id}', name: 'panier_ajouter', methods: ['POST'])]
    public function ajouter(
        int $id,
        Request $request,
        PanierService $panierService
    ): Response {

        $quantite = max(1, (int) $request->request->get('quantite', 1));

        $panierService->ajouter($id, $quantite);

        $this->addFlash(
            'success',
            'Le produit a été ajouté à votre panier.'
        );

        return $this->redirectToRoute('order');
    }

    /**
     * Afficher le panier
     */
    #[Route('/order', name: 'order')]
    public function order(PanierService $panierService): Response
    {
        return $this->render('produit/order.html.twig', [
            'panier' => $panierService->getPanier(),
            'total' => $panierService->getTotal(),
            'nombreArticles' => $panierService->getNombreArticles(),
        ]);
    }

    /**
     * Modifier la quantité
     */
    #[Route('/panier/modifier/{id}', name: 'panier_modifier', methods: ['POST'])]
    public function modifier(
        int $id,
        Request $request,
        PanierService $panierService
    ): Response {

        $quantite = (int) $request->request->get('quantite', 1);

        $panierService->modifier($id, $quantite);

        return $this->redirectToRoute('order');
    }

    /**
     * Supprimer un produit
     */
    #[Route('/panier/supprimer/{id}', name: 'panier_supprimer', methods: ['POST'])]
    public function supprimer(
        int $id,
        PanierService $panierService
    ): Response {

        $panierService->supprimer($id);

        return $this->redirectToRoute('order');
    }

    /**
     * Vider le panier
     */
    #[Route('/panier/vider', name: 'panier_vider', methods: ['POST'])]
    public function vider(PanierService $panierService): Response
    {
        $panierService->vider();

        return $this->redirectToRoute('order');
    }
}