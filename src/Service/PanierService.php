<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    private const SESSION_KEY = 'panier';

    public function __construct(
        private RequestStack $requestStack,
        private ProduitRepository $produitRepository
    ) {
    }

    /**
     * Ajouter un produit au panier
     */
    public function ajouter(int $id, int $quantite = 1): void
    {
        $session = $this->requestStack->getSession();

        $panier = $session->get(self::SESSION_KEY, []);

        if (isset($panier[$id])) {
            $panier[$id] += $quantite;
        } else {
            $panier[$id] = $quantite;
        }

        $session->set(self::SESSION_KEY, $panier);
    }

    /**
     * Modifier la quantité
     */
    public function modifier(int $id, int $quantite): void
    {
        $session = $this->requestStack->getSession();

        $panier = $session->get(self::SESSION_KEY, []);

        if ($quantite <= 0) {
            unset($panier[$id]);
        } else {
            $panier[$id] = $quantite;
        }

        $session->set(self::SESSION_KEY, $panier);
    }

    /**
     * Supprimer un produit
     */
    public function supprimer(int $id): void
    {
        $session = $this->requestStack->getSession();

        $panier = $session->get(self::SESSION_KEY, []);

        unset($panier[$id]);

        $session->set(self::SESSION_KEY, $panier);
    }

    /**
     * Vider le panier
     */
    public function vider(): void
    {
        $this->requestStack->getSession()->remove(self::SESSION_KEY);
    }

    /**
     * Récupérer le contenu complet du panier
     */
    public function getPanier(): array
    {
        $session = $this->requestStack->getSession();

        $panier = $session->get(self::SESSION_KEY, []);

        $resultat = [];

        foreach ($panier as $id => $quantite) {

            $produit = $this->produitRepository->find($id);

            if (!$produit) {
                continue;
            }

            $resultat[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'total' => $produit->getPrixUnitaire() * $quantite,
            ];
        }

        return $resultat;
    }

    /**
     * Nombre total d'articles
     */
    public function getNombreArticles(): int
    {
        $panier = $this->requestStack
            ->getSession()
            ->get(self::SESSION_KEY, []);

        return array_sum($panier);
    }

    /**
     * Total du panier
     */
    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getPanier() as $item) {
            $total += $item['total'];
        }

        return $total;
    }
    
}