<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Form\CheckoutType;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(
        Request $request,
        PanierService $panierService,
        EntityManagerInterface $entityManager
    ): Response {

        $panier = $panierService->getPanier();

        // Impossible de commander avec un panier vide
        if (empty($panier)) {
            $this->addFlash(
                'warning',
                'Votre panier est vide.'
            );

            return $this->redirectToRoute('products');
        }

        $commande = new Commande();

        $form = $this->createForm(CheckoutType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*
             * Numéro de commande
             */
            $numeroCommande = 'LP-' . date('YmdHis') . '-' . random_int(100, 999);

            $commande->setNumeroCommande($numeroCommande);

            /*
             * Statut initial
             */
            $commande->setStatut('A_PAYER');

            /*
             * Calcul du total
             */
            $total = 0;

            foreach ($panier as $item) {

                $produit = $item['produit'];
                $quantite = $item['quantite'];

                $prixUnitaire = (float) $produit->getPrixUnitaire();

                $totalLigne = $prixUnitaire * $quantite;

                $total += $totalLigne;

                /*
                 * Création de la ligne
                 */
                $ligne = new LigneCommande();

                $ligne->setProduit($produit);
                $ligne->setQuantite($quantite);
                $ligne->setPrixUnitaire(
                    number_format($prixUnitaire, 2, '.', '')
                );

                $commande->addLigneCommande($ligne);
            }

            /*
             * Total de la commande
             */
            $commande->setTotal(
                number_format($total, 2, '.', '')
            );

            /*
             * Enregistrement
             */
            $entityManager->persist($commande);
            $entityManager->flush();
            $panierService->vider();

            /*
             * Stocker l'ID de commande en session
             * pour l'étape suivante.
             */
            $request->getSession()->set(
                'commande_id',
                $commande->getId()
            );

            return $this->redirectToRoute(
                'checkout_recap',
                [
                    'id' => $commande->getId()
                ]
            );
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'panier' => $panier,
            'total' => $panierService->getTotal(),
        ]);
    }

    #[Route('/checkout/recap/{id}', name: 'checkout_recap')]
    public function recap(
        Commande $commande
    ): Response {

        return $this->render('checkout/recap.html.twig', [
            'commande' => $commande,
        ]);
    }

}