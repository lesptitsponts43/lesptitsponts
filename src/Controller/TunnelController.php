<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Service\EmailService;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Service\SumUpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Enum\StatutCommande;


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
    public function order(
        Request $request,
        PanierService $panierService,
        SumUpService $sumUp,
        EntityManagerInterface $em,
        EmailService $emailService
    ): Response {
        $lastCommandeId = $request->getSession()->get('last_commande_id');
        $this->syncPendingCommandeStatus($lastCommandeId, $sumUp, $em, $panierService, $emailService);

        return $this->render('produit/order.html.twig', [
            'panier' => $panierService->getPanier(),
            'total' => $panierService->getTotal(),
            'nombreArticles' => $panierService->getNombreArticles(),
        ]);
    }

    private function syncPendingCommandeStatus(
        ?int $commandeId,
        SumUpService $sumUp,
        EntityManagerInterface $em,
        ?PanierService $panierService = null,
        ?EmailService $emailService = null
    ): void {
        if (!$commandeId) {
            return;
        }

        $commande = $em->getRepository(Commande::class)->find($commandeId);

        if (!$commande || $commande->getStatut() !== StatutCommande::A_PAYER || !$commande->getSumupCheckoutId()) {
            return;
        }

        $status = strtoupper((string) $sumUp->getCheckoutStatus($commande->getSumupCheckoutId()));

        if ($status === 'PAID' || $status === 'PAYMENT_ACCEPTED') {
            $commande->setStatut(StatutCommande::PAYEE);
            $commande->setModePaiement('SumUp');
            $em->flush();

            if ($emailService) {
                $emailService->sendPaymentConfirmation($commande);
                $emailService->sendAdminNewOrder($commande);
            }

            if ($panierService) {
                $panierService->vider();
            }

            return;
        }

        if (in_array($status, ['FAILED', 'FAILURE', 'DECLINED', 'DECLINED_PAYMENT', 'ERROR'], true)) {
            $commande->setStatut(StatutCommande::ECHEC);
            $em->flush();
            return;
        }

        if (in_array($status, ['REJECTED', 'REFUSED', 'REJECTED_PAYMENT'], true)) {
            $commande->setStatut(StatutCommande::REFUSE);
            $em->flush();
            return;
        }

        if (in_array($status, ['CANCELLED', 'CANCELED'], true)) {
            $commande->setStatut(StatutCommande::ANNULEE);
            $em->flush();
        }
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

    #[Route('/commande/paiement/{id}', name: 'order_payment')]
    public function pay(
        Commande $commande,
        SumUpService $sumUp,
        EntityManagerInterface $em
    ): Response {

        $checkout = $sumUp->createHostedCheckout(
            reference: $commande->getNumeroCommande(),
            amount: (float) $commande->getTotal(),
            currency: 'EUR',
            redirectUrl: $this->generateUrl(
                'order_confirmation',
                ['id' => $commande->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            returnUrl: $this->generateUrl('sumup_webhook', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $commande->setSumupCheckoutId($checkout['id']);
        $em->flush();

        return $this->redirect($checkout['url']);
    }

    #[Route('/commande/confirmation/{id}', name: 'order_confirmation')]
    public function confirmation(
        Commande $commande,
        SumUpService $sumUp,
        EntityManagerInterface $em,
        PanierService $panierService,
        EmailService $emailService
    ): Response {

        if ($commande->getSumupCheckoutId() && $commande->getStatut() === StatutCommande::A_PAYER) {
            $status = strtoupper((string) $sumUp->getCheckoutStatus($commande->getSumupCheckoutId()));

            if ($status === 'PAID' || $status === 'PAYMENT_ACCEPTED') {
                $commande->setStatut(StatutCommande::PAYEE);
                $commande->setModePaiement('SumUp');
                $em->flush();

                $emailService->sendPaymentConfirmation($commande);
                $emailService->sendAdminNewOrder($commande);

                $panierService->vider();
            } elseif (in_array($status, ['FAILED', 'FAILURE', 'DECLINED', 'DECLINED_PAYMENT', 'ERROR'], true)) {
                $commande->setStatut(StatutCommande::ECHEC);
                $em->flush();
            } elseif (in_array($status, ['REJECTED', 'REFUSED', 'REJECTED_PAYMENT'], true)) {
                $commande->setStatut(StatutCommande::REFUSE);
                $em->flush();
            } elseif (in_array($status, ['CANCELLED', 'CANCELED'], true)) {
                $commande->setStatut(StatutCommande::ANNULEE);
                $em->flush();
            }
        }

        return $this->render('produit/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }
    #[Route('/checkout', name: 'checkout')]
    public function checkout(
        Request $request,
        PanierService $panierService,
        SumUpService $sumUp,
        EntityManagerInterface $em,
        EmailService $emailService
    ): Response {
        $lastCommandeId = $request->getSession()->get('last_commande_id');
        $this->syncPendingCommandeStatus($lastCommandeId, $sumUp, $em, $panierService, $emailService);

        $panier = $panierService->getPanier();

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('order');
        }

        return $this->render('produit/checkout.html.twig', [
            'panier' => $panier,
            'total' => $panierService->getTotal(),
            'nombreArticles' => $panierService->getNombreArticles(),
        ]);
    }
    #[Route('/commande/valider', name: 'commande_valider', methods: ['POST'])]
    public function validerCommande(
        Request $request,
        PanierService $panierService,
        EntityManagerInterface $em
    ): Response {

        $panier = $panierService->getPanier();

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('order');
        }

        $commande = new Commande();
        $commande->setNumeroCommande('CMD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4))));
        $commande->setNom($request->request->get('nom'));
        $commande->setPrenom($request->request->get('prenom'));
        $commande->setEmail($request->request->get('email'));
        $commande->setTelephone($request->request->get('telephone'));
        $commande->setTotal((string) $panierService->getTotal());

        foreach ($panier as $item) {
            $ligne = new LigneCommande();
            $ligne->setProduit($item['produit']);
            $ligne->setQuantite($item['quantite']);
            $ligne->setPrixUnitaire($item['produit']->getPrixUnitaire());
            $commande->addLigneCommande($ligne);
        }

        $em->persist($commande);
        $em->flush();

        $request->getSession()->set('last_commande_id', $commande->getId());

        return $this->redirectToRoute('order_payment', ['id' => $commande->getId()]);
    }
}